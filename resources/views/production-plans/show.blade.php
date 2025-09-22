@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $productionPlan->name }}</h4>
                    <div>
                        <span class="badge {{ $productionPlan->getStatusBadgeClass() }}">
                            {{ ucfirst(str_replace('_', ' ', $productionPlan->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Plan Information</h6>
                            <p><strong>Plan Number:</strong> {{ $productionPlan->plan_number }}</p>
                            <p><strong>Description:</strong> {{ $productionPlan->description ?? 'N/A' }}</p>
                            <p><strong>Created By:</strong> {{ $productionPlan->createdBy->name }}</p>
                            <p><strong>Created:</strong> {{ $productionPlan->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Timeline</h6>
                            <p><strong>Planned Start:</strong> {{ $productionPlan->planned_start_date->format('M d, Y') }}</p>
                            <p><strong>Planned End:</strong> {{ $productionPlan->planned_end_date->format('M d, Y') }}</p>
                            @if($productionPlan->actual_start_date)
                                <p><strong>Actual Start:</strong> {{ $productionPlan->actual_start_date->format('M d, Y') }}</p>
                            @endif
                            @if($productionPlan->actual_end_date)
                                <p><strong>Actual End:</strong> {{ $productionPlan->actual_end_date->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Cost Summary</h6>
                                    <p><strong>Estimated Cost:</strong> ${{ number_format($productionPlan->total_estimated_cost, 2) }}</p>
                                    <p><strong>Actual Cost:</strong> ${{ number_format($productionPlan->total_actual_cost, 2) }}</p>
                                    @if($productionPlan->total_estimated_cost > 0)
                                        <p><strong>Variance:</strong> 
                                            <span class="{{ ($productionPlan->total_actual_cost - $productionPlan->total_estimated_cost) <= 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($productionPlan->total_actual_cost - $productionPlan->total_estimated_cost, 2) }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Progress</h6>
                                    <p><strong>Total Items:</strong> {{ $productionPlan->productionPlanItems->count() }}</p>
                                    <p><strong>Completed:</strong> {{ $productionPlan->productionPlanItems->where('status', 'completed')->count() }}</p>
                                    <p><strong>In Progress:</strong> {{ $productionPlan->productionPlanItems->where('status', 'in_progress')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($productionPlan->notes)
                        <div class="mb-4">
                            <h6>Notes</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($productionPlan->notes)) !!}
                            </div>
                        </div>
                    @endif

                    <h6>Production Items</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Recipe</th>
                                    <th>Planned Qty</th>
                                    <th>Actual Qty</th>
                                    <th>Est. Cost</th>
                                    <th>Actual Cost</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productionPlan->productionPlanItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->recipe?->name ?? 'N/A' }}</td>
                                        <td>{{ $item->planned_quantity }} {{ $item->unit }}</td>
                                        <td>{{ $item->actual_quantity }} {{ $item->unit }}</td>
                                        <td>${{ number_format($item->estimated_material_cost, 2) }}</td>
                                        <td>${{ number_format($item->actual_material_cost, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $item->getStatusBadgeClass() }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->priority }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if($productionPlan->status === 'draft')
                            <a href="{{ route('production-plans.edit', $productionPlan) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Plan
                            </a>
                            <form action="{{ route('production-plans.approve', $productionPlan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve Plan
                                </button>
                            </form>
                        @endif

                        @if($productionPlan->status === 'approved')
                            <form action="{{ route('production-plans.start', $productionPlan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Start Production
                                </button>
                            </form>
                        @endif

                        @if($productionPlan->status === 'in_progress')
                            <form action="{{ route('production-plans.complete', $productionPlan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-flag-checkered"></i> Complete Plan
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('production-plans.material-requirements', $productionPlan) }}" class="btn btn-info">
                            <i class="fas fa-list"></i> Material Requirements
                        </a>

                        <a href="{{ route('production-plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Material Requirements Summary</h5>
                </div>
                <div class="card-body">
                    @if($materialRequirements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Required</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materialRequirements as $requirement)
                                        <tr>
                                            <td>{{ $requirement['raw_material']->name }}</td>
                                            <td>{{ number_format($requirement['total_required'], 2) }} {{ $requirement['unit'] }}</td>
                                            <td>${{ number_format($requirement['estimated_cost'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <th colspan="2">Total</th>
                                        <th>${{ number_format($materialRequirements->sum('estimated_cost'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No material requirements calculated</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection