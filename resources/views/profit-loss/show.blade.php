@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Profit & Loss Statement') }}</h2>
                <div>
                    @if($profitLoss->status == 'draft')
                        <a href="{{ route('profit-loss.edit', $profitLoss) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('profit-loss.finalize', $profitLoss) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success me-2" onclick="return confirm('Finalize this statement? This cannot be undone.')">
                                <i class="fas fa-check"></i> {{ __('Finalize') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('profit-loss.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Financial Summary') }}</h5>
                            <small class="text-muted">
                                {{ __('Period:') }} {{ $profitLoss->period_start->format('M d, Y') }} - {{ $profitLoss->period_end->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-success">{{ __('REVENUE') }}</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>{{ __('Total Sales') }}</td>
                                            <td class="text-end text-success">${{ number_format($profitLoss->total_revenue, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-danger">{{ __('EXPENSES') }}</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>{{ __('Cost of Goods Sold') }}</td>
                                            <td class="text-end">${{ number_format($profitLoss->cost_of_goods_sold, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('Staff Costs') }}</td>
                                            <td class="text-end">${{ number_format($profitLoss->staff_costs, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('Operating Expenses') }}</td>
                                            <td class="text-end">${{ number_format($profitLoss->operating_expenses, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>{{ __('Total Expenses') }}</strong></td>
                                            <td class="text-end text-danger"><strong>${{ number_format($profitLoss->total_expenses, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table">
                                        <tr>
                                            <td><strong>{{ __('Gross Profit') }}</strong></td>
                                            <td class="text-end {{ $profitLoss->gross_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>${{ number_format($profitLoss->gross_profit, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td><strong>{{ __('NET PROFIT') }}</strong></td>
                                            <td class="text-end {{ $profitLoss->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>${{ number_format($profitLoss->net_profit, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('Statement Info') }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Status:') }}</strong> 
                                <span class="badge bg-{{ $profitLoss->status == 'finalized' ? 'success' : 'warning' }}">
                                    {{ ucfirst($profitLoss->status) }}
                                </span>
                            </p>
                            <p><strong>{{ __('Created By:') }}</strong> {{ $profitLoss->creator->name }}</p>
                            <p><strong>{{ __('Created:') }}</strong> {{ $profitLoss->created_at->format('M d, Y H:i') }}</p>
                            @if($profitLoss->updated_at != $profitLoss->created_at)
                                <p><strong>{{ __('Updated:') }}</strong> {{ $profitLoss->updated_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6>{{ __('Key Metrics') }}</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $profitMargin = $profitLoss->total_revenue > 0 ? ($profitLoss->net_profit / $profitLoss->total_revenue) * 100 : 0;
                                $grossMargin = $profitLoss->total_revenue > 0 ? ($profitLoss->gross_profit / $profitLoss->total_revenue) * 100 : 0;
                            @endphp
                            <p><strong>{{ __('Profit Margin:') }}</strong> 
                                <span class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($profitMargin, 1) }}%
                                </span>
                            </p>
                            <p><strong>{{ __('Gross Margin:') }}</strong> 
                                <span class="{{ $grossMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($grossMargin, 1) }}%
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($profitLoss->revenue_breakdown || $profitLoss->expense_breakdown)
                <div class="row mt-4">
                    @if($profitLoss->revenue_breakdown)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>{{ __('Revenue Breakdown') }}</h6>
                                </div>
                                <div class="card-body">
                                    @php $breakdown = $profitLoss->revenue_breakdown; @endphp
                                    <p><strong>{{ __('Total Sales:') }}</strong> ${{ number_format($breakdown['total_sales'] ?? 0, 2) }}</p>
                                    <p><strong>{{ __('Number of Sales:') }}</strong> {{ $breakdown['sales_count'] ?? 0 }}</p>
                                    <p><strong>{{ __('Average Sale:') }}</strong> ${{ number_format($breakdown['average_sale'] ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($profitLoss->expense_breakdown)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>{{ __('Expense Breakdown') }}</h6>
                                </div>
                                <div class="card-body">
                                    @php $breakdown = $profitLoss->expense_breakdown; @endphp
                                    <p><strong>{{ __('COGS:') }}</strong> ${{ number_format($breakdown['cost_of_goods_sold'] ?? 0, 2) }}</p>
                                    <p><strong>{{ __('Staff Costs:') }}</strong> ${{ number_format($breakdown['staff_costs'] ?? 0, 2) }}</p>
                                    <p><strong>{{ __('Operating Expenses:') }}</strong> ${{ number_format($breakdown['operating_expenses'] ?? 0, 2) }}</p>
                                    <p><strong>{{ __('Staff Days:') }}</strong> {{ $breakdown['staff_days'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection