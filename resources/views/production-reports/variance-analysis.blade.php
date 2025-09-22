@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Variance Analysis Report</h4>
                    <div>
                        <a href="{{ route('production-reports.variance-analysis.export', request()->all()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('production-reports.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date *</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ $request->start_date }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>End Date *</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ $request->end_date }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>Product</label>
                                <select name="product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ $request->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>

                    @if(isset($varianceData))
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>{{ $summary['total_items'] }}</h5>
                                        <p class="mb-0">Total Items</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>{{ number_format($summary['average_efficiency'], 1) }}%</h5>
                                        <p class="mb-0">Avg Efficiency</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>{{ $summary['items_under_budget'] }}</h5>
                                        <p class="mb-0">Under Budget</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>{{ $summary['items_over_budget'] }}</h5>
                                        <p class="mb-0">Over Budget</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Variance Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Product</th>
                                        <th>Planned Qty</th>
                                        <th>Actual Qty</th>
                                        <th>Qty Variance</th>
                                        <th>Qty Variance %</th>
                                        <th>Est. Cost</th>
                                        <th>Actual Cost</th>
                                        <th>Cost Variance</th>
                                        <th>Cost Variance %</th>
                                        <th>Efficiency %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($varianceData as $item)
                                        <tr>
                                            <td>{{ $item['production_plan'] }}</td>
                                            <td>{{ $item['product'] }}</td>
                                            <td>{{ number_format($item['planned_quantity'], 2) }}</td>
                                            <td>{{ number_format($item['actual_quantity'], 2) }}</td>
                                            <td class="{{ $item['quantity_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($item['quantity_variance'], 2) }}
                                            </td>
                                            <td class="{{ $item['quantity_variance_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($item['quantity_variance_percentage'], 1) }}%
                                            </td>
                                            <td>${{ number_format($item['estimated_cost'], 2) }}</td>
                                            <td>${{ number_format($item['actual_cost'], 2) }}</td>
                                            <td class="{{ $item['cost_variance'] <= 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($item['cost_variance'], 2) }}
                                            </td>
                                            <td class="{{ $item['cost_variance_percentage'] <= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($item['cost_variance_percentage'], 1) }}%
                                            </td>
                                            <td class="{{ $item['efficiency'] >= 100 ? 'text-success' : ($item['efficiency'] >= 90 ? 'text-warning' : 'text-danger') }}">
                                                {{ number_format($item['efficiency'], 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th colspan="2">TOTALS</th>
                                        <th>{{ number_format($summary['total_planned_quantity'], 2) }}</th>
                                        <th>{{ number_format($summary['total_actual_quantity'], 2) }}</th>
                                        <th class="{{ ($summary['total_actual_quantity'] - $summary['total_planned_quantity']) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($summary['total_actual_quantity'] - $summary['total_planned_quantity'], 2) }}
                                        </th>
                                        <th>-</th>
                                        <th>${{ number_format($summary['total_estimated_cost'], 2) }}</th>
                                        <th>${{ number_format($summary['total_actual_cost'], 2) }}</th>
                                        <th class="{{ ($summary['total_actual_cost'] - $summary['total_estimated_cost']) <= 0 ? 'text-success' : 'text-danger' }}">
                                            ${{ number_format($summary['total_actual_cost'] - $summary['total_estimated_cost'], 2) }}
                                        </th>
                                        <th>-</th>
                                        <th>{{ number_format($summary['average_efficiency'], 1) }}%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection