<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,pending,partial',
            'payment_method' => 'required_if:payment_status,paid,partial',
            'amount_paid' => 'required_if:payment_status,paid,partial|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
    
        try {
            DB::transaction(function () use ($request) {
                // Create purchase record
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'payment_status' => $request->payment_status,
                    'payment_method' => $request->payment_method,
                    'amount_paid' => $request->amount_paid ?? 0,
                    'notes' => $request->notes,
                    'created_by' => auth()->id()
                ]);
    
                $totalAmount = 0;
    
                // Create purchase items and update raw material stock
                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $itemTotal;
    
                    // Create purchase item
                    $purchase->purchaseItems()->create([
                        'raw_material_id' => $item['raw_material_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_amount' => $itemTotal
                    ]);
    
                    // Update raw material stock and record movement
                    $rawMaterial = RawMaterial::findOrFail($item['raw_material_id']);
                    $rawMaterial->quantity += $item['quantity'];
                    $rawMaterial->cost_per_unit = $item['unit_price'];
                    $rawMaterial->last_purchase_date = $request->purchase_date;
                    $rawMaterial->last_purchase_price = $item['unit_price'];
                    $rawMaterial->save();
    
                    // Record revenue
                    Revenue::create([
                        'date' => $request->purchase_date,
                        'amount' => $itemTotal,
                        'type' => 'raw_material_purchase',
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'notes' => "Purchase of {$rawMaterial->name}"
                    ]);
    
                    // Record stock movement
                    StockMovement::create([
                        'raw_material_id' => $rawMaterial->id,
                        'movement_type' => 'purchase',
                        'quantity' => $item['quantity'],
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'unit_price' => $item['unit_price'],
                        'total_amount' => $itemTotal,
                        'notes' => "Purchase #{$purchase->id}"
                    ]);
                }
    
                // Update purchase total
                $purchase->update([
                    'total_amount' => $totalAmount,
                    'balance_due' => $totalAmount - ($request->amount_paid ?? 0)
                ]);
            });
    
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase recorded successfully with stock and revenue updates.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error recording purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');
        return view('purchases.show', compact('purchase'));
    }
}
