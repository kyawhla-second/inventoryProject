<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitLossStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_start',
        'period_end',
        'total_revenue',
        'cost_of_goods_sold',
        'gross_profit',
        'staff_costs',
        'operating_expenses',
        'total_expenses',
        'net_profit',
        'revenue_breakdown',
        'expense_breakdown',
        'status',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_revenue' => 'decimal:2',
        'cost_of_goods_sold' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'staff_costs' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'revenue_breakdown' => 'array',
        'expense_breakdown' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculateProfitLoss()
    {
        // Calculate revenue from sales
        $this->total_revenue = Sale::whereBetween('sale_date', [$this->period_start, $this->period_end])
            ->sum('total_amount');

        // Calculate COGS from purchases
        $this->cost_of_goods_sold = Purchase::whereBetween('purchase_date', [$this->period_start, $this->period_end])
            ->sum('total_amount');

        // Calculate staff costs
        $this->staff_costs = StaffDailyCharge::forPeriod($this->period_start, $this->period_end)
            ->byStatus('approved')
            ->sum('total_charge');

        // Calculate gross profit
        $this->gross_profit = $this->total_revenue - $this->cost_of_goods_sold;

        // Calculate total expenses
        $this->total_expenses = $this->cost_of_goods_sold + $this->staff_costs + $this->operating_expenses;

        // Calculate net profit
        $this->net_profit = $this->total_revenue - $this->total_expenses;

        return $this;
    }

    public function getRevenueBreakdown()
    {
        $sales = Sale::with('customer')
            ->whereBetween('sale_date', [$this->period_start, $this->period_end])
            ->get();

        return [
            'total_sales' => $sales->sum('total_amount'),
            'sales_count' => $sales->count(),
            'average_sale' => $sales->avg('total_amount'),
            'by_customer' => $sales->groupBy('customer_id')->map(function ($customerSales) {
                return [
                    'customer_name' => $customerSales->first()->customer->name ?? 'Walk-in',
                    'total_amount' => $customerSales->sum('total_amount'),
                    'sales_count' => $customerSales->count(),
                ];
            })->values(),
        ];
    }

    public function getExpenseBreakdown()
    {
        $purchases = Purchase::whereBetween('purchase_date', [$this->period_start, $this->period_end])->get();
        $staffCharges = StaffDailyCharge::forPeriod($this->period_start, $this->period_end)->get();

        return [
            'cost_of_goods_sold' => $purchases->sum('total_amount'),
            'staff_costs' => $staffCharges->sum('total_charge'),
            'operating_expenses' => $this->operating_expenses,
            'purchases_count' => $purchases->count(),
            'staff_days' => $staffCharges->count(),
        ];
    }
}