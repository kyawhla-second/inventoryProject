<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'sale_id',
        'order_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'billing_address',
        'shipping_address',
        'payment_terms',
        'created_by',
        'printed_at',
        'print_count',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'printed_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "INV-{$year}{$month}-";
        
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        // Calculate discount
        if ($this->discount_rate > 0) {
            $this->discount_amount = ($this->subtotal * $this->discount_rate) / 100;
        }

        $subtotalAfterDiscount = $this->subtotal - $this->discount_amount;

        // Calculate tax
        if ($this->tax_rate > 0) {
            $this->tax_amount = ($subtotalAfterDiscount * $this->tax_rate) / 100;
        }

        $this->total_amount = $subtotalAfterDiscount + $this->tax_amount;

        return $this;
    }

    public function markAsPrinted()
    {
        $this->printed_at = now();
        $this->print_count += 1;
        $this->save();
    }

    public function isOverdue()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'sent' => 'bg-info',
            'paid' => 'bg-success',
            'overdue' => 'bg-danger',
            'cancelled' => 'bg-dark',
            default => 'bg-secondary'
        };
    }

    public function createFromSale(Sale $sale)
    {
        $invoice = new self([
            'invoice_number' => self::generateInvoiceNumber(),
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $sale->total_amount,
            'total_amount' => $sale->total_amount,
            'created_by' => auth()->id(),
            'payment_terms' => 'Net 30',
        ]);

        $invoice->save();

        // Create invoice items from sale items
        foreach ($sale->items as $saleItem) {
            $invoice->items()->create([
                'product_id' => $saleItem->product_id,
                'description' => $saleItem->product->name,
                'quantity' => $saleItem->quantity,
                'unit_price' => $saleItem->unit_price,
                'total_price' => $saleItem->quantity * $saleItem->unit_price,
            ]);
        }

        $invoice->calculateTotals();
        $invoice->save();

        return $invoice;
    }

    public function createFromOrder(Order $order)
    {
        $invoice = new self([
            'invoice_number' => self::generateInvoiceNumber(),
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $order->total_amount,
            'total_amount' => $order->total_amount,
            'created_by' => auth()->id(),
            'payment_terms' => 'Net 30',
        ]);

        $invoice->save();

        // Create invoice items from order items
        foreach ($order->items as $orderItem) {
            $invoice->items()->create([
                'product_id' => $orderItem->product_id,
                'description' => $orderItem->product->name,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->price,
                'total_price' => $orderItem->quantity * $orderItem->price,
            ]);
        }

        $invoice->calculateTotals();
        $invoice->save();

        return $invoice;
    }
}