<?php

namespace App\Http\Controllers;

use App\Models\ProfitLossStatement;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\StaffDailyCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statements = ProfitLossStatement::with('creator')
            ->orderBy('period_start', 'desc')
            ->paginate(10);

        return view('profit-loss.index', compact('statements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profit-loss.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'operating_expenses' => 'nullable|numeric|min:0',
        ]);

        $statement = new ProfitLossStatement([
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'operating_expenses' => $request->operating_expenses ?? 0,
            'created_by' => Auth::id(),
        ]);

        $statement->calculateProfitLoss();
        $statement->revenue_breakdown = $statement->getRevenueBreakdown();
        $statement->expense_breakdown = $statement->getExpenseBreakdown();
        $statement->save();

        return redirect()->route('profit-loss.show', $statement)
            ->with('success', 'Profit & Loss statement generated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfitLossStatement $profitLoss)
    {
        $profitLoss->load('creator');
        return view('profit-loss.show', compact('profitLoss'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfitLossStatement $profitLoss)
    {
        return view('profit-loss.edit', compact('profitLoss'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProfitLossStatement $profitLoss)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'operating_expenses' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,finalized',
        ]);

        $profitLoss->update([
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'operating_expenses' => $request->operating_expenses ?? 0,
            'status' => $request->status,
        ]);

        $profitLoss->calculateProfitLoss();
        $profitLoss->revenue_breakdown = $profitLoss->getRevenueBreakdown();
        $profitLoss->expense_breakdown = $profitLoss->getExpenseBreakdown();
        $profitLoss->save();

        return redirect()->route('profit-loss.show', $profitLoss)
            ->with('success', 'Profit & Loss statement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfitLossStatement $profitLoss)
    {
        $profitLoss->delete();

        return redirect()->route('profit-loss.index')
            ->with('success', 'Profit & Loss statement deleted successfully.');
    }

    /**
     * Generate quick report for current month
     */
    public function quickReport()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = [
            'period_start' => $startOfMonth,
            'period_end' => $endOfMonth,
            'total_revenue' => Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('total_amount'),
            'cost_of_goods_sold' => Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])->sum('total_amount'),
            'staff_costs' => StaffDailyCharge::forPeriod($startOfMonth, $endOfMonth)->byStatus('approved')->sum('total_charge'),
        ];

        $data['gross_profit'] = $data['total_revenue'] - $data['cost_of_goods_sold'];
        $data['total_expenses'] = $data['cost_of_goods_sold'] + $data['staff_costs'];
        $data['net_profit'] = $data['total_revenue'] - $data['total_expenses'];

        return view('profit-loss.quick-report', compact('data'));
    }

    /**
     * Finalize a profit loss statement
     */
    public function finalize(ProfitLossStatement $profitLoss)
    {
        $profitLoss->update(['status' => 'finalized']);

        return redirect()->back()
            ->with('success', 'Profit & Loss statement finalized successfully.');
    }
}
