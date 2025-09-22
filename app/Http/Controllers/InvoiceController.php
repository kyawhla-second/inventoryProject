<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forPeriod($request->start_date, $request->end_date);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Check for overdue invoices
        if ($request->get('show_overdue')) {
            $query->overdue();
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);
        $customers = Customer::all();

        return view('invoices.index', compact('invoices', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::all();
        $products = Product::where('quantity', '>', 0)->get();
        $sale = null;

        // If creating from a sale
        if ($request->filled('sale_id')) {
            $sale = Sale::with(['items.product', 'customer'])->findOrFail($request->sale_id);
        }

        return view('invoices.create', compact('customers', 'products', 'sale'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'payment_terms' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'sale_id' => $request->sale_id,
            'customer_id' => $request->customer_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'tax_rate' => $request->tax_rate ?? 0,
            'discount_rate' => $request->discount_rate ?? 0,
            'notes' => $request->notes,
            'payment_terms' => $request->payment_terms ?? 'Net 30',
            'created_by' => Auth::id(),
            'subtotal' => 0,
            'total_amount' => 0,
        ]);

        // Create invoice items
        foreach ($request->items as $itemData) {
            $item = new InvoiceItem($itemData);
            $item->calculateTotal();
            $invoice->items()->save($item);
        }

        // Calculate totals
        $invoice->calculateTotals();
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'creator', 'sale']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot edit paid invoices.');
        }

        $invoice->load(['items.product']);
        $customers = Customer::all();
        $products = Product::where('quantity', '>', 0)->get();

        return view('invoices.edit', compact('invoice', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot edit paid invoices.');
        }

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'customer_id' => $request->customer_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'tax_rate' => $request->tax_rate ?? 0,
            'discount_rate' => $request->discount_rate ?? 0,
            'notes' => $request->notes,
            'payment_terms' => $request->payment_terms ?? 'Net 30',
            'status' => $request->status,
        ]);

        // Delete existing items and create new ones
        $invoice->items()->delete();

        foreach ($request->items as $itemData) {
            $item = new InvoiceItem($itemData);
            $item->calculateTotal();
            $invoice->items()->save($item);
        }

        // Recalculate totals
        $invoice->calculateTotals();
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.index')
                ->with('error', 'Cannot delete paid invoices.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'creator']);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Print invoice (generate PDF and mark as printed)
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'creator']);
        
        // Mark as printed
        $invoice->markAsPrinted();
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Send invoice to printer (for direct printing)
     */
    public function sendToPrinter(Invoice $invoice, Request $request)
    {
        $invoice->load(['customer', 'items.product', 'creator']);
        
        // Generate PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdfContent = $pdf->output();
        
        // Save temporary PDF file
        $tempPath = storage_path('app/temp/invoice-' . $invoice->invoice_number . '.pdf');
        
        // Ensure temp directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        file_put_contents($tempPath, $pdfContent);
        
        // Mark as printed
        $invoice->markAsPrinted();
        
        // For Windows, use the default printer
        if (PHP_OS_FAMILY === 'Windows') {
            $printerName = $request->get('printer_name', 'default');
            
            if ($printerName === 'default') {
                // Print to default printer
                $command = 'powershell -Command "Start-Process -FilePath \'' . $tempPath . '\' -Verb Print"';
            } else {
                // Print to specific printer
                $command = 'powershell -Command "Start-Process -FilePath \'' . $tempPath . '\' -ArgumentList \'/t\',\'/p:' . $printerName . '\'"';
            }
            
            exec($command, $output, $returnCode);
            
            // Clean up temp file after a delay
            register_shutdown_function(function() use ($tempPath) {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            });
            
            if ($returnCode === 0) {
                return redirect()->back()->with('success', 'Invoice sent to printer successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to send invoice to printer.');
            }
        }
        
        // For other OS, just download the PDF
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    /**
     * Get available printers (Windows only)
     */
    public function getPrinters()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return response()->json(['printers' => []]);
        }
        
        $command = 'powershell -Command "Get-Printer | Select-Object Name | ConvertTo-Json"';
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $printersJson = implode('', $output);
            $printers = json_decode($printersJson, true);
            
            if (is_array($printers)) {
                $printerNames = array_column($printers, 'Name');
            } else {
                $printerNames = [$printers['Name'] ?? 'Default Printer'];
            }
        } else {
            $printerNames = ['Default Printer'];
        }
        
        return response()->json(['printers' => $printerNames]);
    }

    /**
     * Create invoice from sale
     */
    public function createFromSale(Sale $sale)
    {
        // Check if invoice already exists for this sale
        if ($sale->invoice) {
            return redirect()->route('invoices.show', $sale->invoice)
                ->with('info', 'Invoice already exists for this sale.');
        }

        $invoice = (new Invoice())->createFromSale($sale);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created from sale successfully.');
    }

    /**
     * Create invoice from order
     */
    public function createFromOrder(Order $order)
    {
        // Check if invoice already exists for this order
        if ($order->invoice) {
            return redirect()->route('invoices.show', $order->invoice)
                ->with('info', 'Invoice already exists for this order.');
        }

        $invoice = (new Invoice())->createFromOrder($order);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created from order successfully.');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);

        return redirect()->back()
            ->with('success', 'Invoice marked as paid successfully.');
    }

    /**
     * Send invoice (mark as sent)
     */
    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);

        return redirect()->back()
            ->with('success', 'Invoice marked as sent successfully.');
    }

    /**
     * Preview invoice before printing
     */
    public function preview(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'creator']);
        return view('invoices.preview', compact('invoice'));
    }
}
