<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($search = $request->input('q')) {
            $query->where('id', $search)
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products  = \App\Models\Product::all();
        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date'  => 'required|date',
            'products'               => 'required|array',
            'products.*.product_id'  => 'required|exists:products,id',
            'products.*.quantity'    => 'required|integer|min:1',
            'products.*.price'       => 'required|numeric|min:0',
        ]);

        $order = \DB::transaction(function () use ($request) {
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $order = Order::create([
                'customer_id'   => $request->customer_id,
                'order_date'    => $request->order_date,
                'total_amount'  => $totalAmount,
                'status'        => 'pending',
            ]);

            foreach ($request->products as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                // deduct product stock
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('quantity', $item['quantity']);
                }
            }

            return $order;
        });

        // Automatically create invoice for the order
        if ($order) {
            $invoice = (new \App\Models\Invoice())->createFromOrder($order);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order and invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update($request->only('status'));

        return redirect()->route('orders.index')
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Convert order to sale
     */
    public function convertToSale(Order $order)
    {
        try {
            $sale = $order->convertToSale();
            
            // Update order status
            $order->update(['status' => 'completed']);
            
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Order converted to sale successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to convert order to sale: ' . $e->getMessage());
        }
    }

    /**
     * Show form to create purchase order from customer order
     */
    public function createPurchaseForm(Order $order)
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        
        return view('orders.create-purchase', compact('order', 'suppliers', 'products'));
    }

    /**
     * Create purchase order from customer order
     */
    public function createPurchase(Request $request, Order $order)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.description' => 'required|string|max:255',
        ]);

        try {
            $purchase = $order->createPurchaseOrder($request->supplier_id, $request->items);
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order created successfully from customer order.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }
}
