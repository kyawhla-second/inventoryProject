@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Quick Profit & Loss Report') }}</h2>
                <div>
                    <a href="{{ route('profit-loss.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus"></i> {{ __('Generate Full Statement') }}
                    </a>
                    <a href="{{ route('profit-loss.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Statements') }}
                    </a>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                {{ __('This is a quick overview for the current month. For detailed statements and historical records, generate a full Profit & Loss statement.') }}
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Current Month Summary') }}</h5>
                    <small class="text-muted">
                        {{ __('Period:') }} {{ $data['period_start']->format('M d, Y') }} - {{ $data['period_end']->format('M d, Y') }}
                    </small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($data['total_revenue'], 2) }}</h4>
                                    <p class="mb-0">{{ __('Total Revenue') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($data['cost_of_goods_sold'], 2) }}</h4>
                                    <p class="mb-0">{{ __('Cost of Goods Sold') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($data['staff_costs'], 2) }}</h4>
                                    <p class="mb-0">{{ __('Staff Costs') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-{{ $data['net_profit'] >= 0 ? 'success' : 'danger' }} text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($data['net_profit'], 2) }}</h4>
                                    <p class="mb-0">{{ __('Net Profit') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">{{ __('REVENUE ANALYSIS') }}</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>{{ __('Total Sales') }}</td>
                                    <td class="text-end text-success">${{ number_format($data['total_revenue'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Gross Profit') }}</td>
                                    <td class="text-end {{ $data['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ${{ number_format($data['gross_profit'], 2) }}
                                    </td>
                                </tr>
                                @php
                                    $grossMargin = $data['total_revenue'] > 0 ? ($data['gross_profit'] / $data['total_revenue']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>{{ __('Gross Margin') }}</td>
                                    <td class="text-end {{ $grossMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($grossMargin, 1) }}%
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger">{{ __('EXPENSE ANALYSIS') }}</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>{{ __('Cost of Goods Sold') }}</td>
                                    <td class="text-end">${{ number_format($data['cost_of_goods_sold'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Staff Costs') }}</td>
                                    <td class="text-end">${{ number_format($data['staff_costs'], 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>{{ __('Total Expenses') }}</strong></td>
                                    <td class="text-end text-danger"><strong>${{ number_format($data['total_expenses'], 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h5 class="text-success">${{ number_format($data['total_revenue'], 2) }}</h5>
                                            <small>{{ __('Revenue') }}</small>
                                        </div>
                                        <div class="col-md-1">
                                            <h5>-</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="text-danger">${{ number_format($data['total_expenses'], 2) }}</h5>
                                            <small>{{ __('Expenses') }}</small>
                                        </div>
                                        <div class="col-md-1">
                                            <h5>=</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="{{ $data['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($data['net_profit'], 2) }}
                                            </h4>
                                            <small><strong>{{ __('Net Profit') }}</strong></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $profitMargin = $data['total_revenue'] > 0 ? ($data['net_profit'] / $data['total_revenue']) * 100 : 0;
                    @endphp
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <h6>{{ __('Profit Margin:') }} 
                                <span class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($profitMargin, 1) }}%
                                </span>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection