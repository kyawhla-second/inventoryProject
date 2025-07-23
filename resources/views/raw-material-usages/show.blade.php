@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Raw Material Usage Details') }}</h2>
                <div>
                    <a href="{{ route('raw-material-usages.edit', $rawMaterialUsage) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('raw-material-usages.index') }}" class="btn btn-secondary">
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
                            <h5>{{ __('Usage Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>{{ __('Raw Material Details') }}</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <i class="fas fa-cube text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $rawMaterialUsage->rawMaterial->name }}</h5>
                                            <small class="text-muted">{{ $rawMaterialUsage->rawMaterial->category ?? 'No Category' }}</small>
                                        </div>
                                    </div>
                                    <p><strong>{{ __('Current Stock:') }}</strong> {{ number_format($rawMaterialUsage->rawMaterial->quantity, 2) }} {{ $rawMaterialUsage->rawMaterial->unit }}</p>
                                    <p><strong>{{ __('Unit:') }}</strong> {{ $rawMaterialUsage->rawMaterial->unit }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>{{ __('Usage Details') }}</h6>
                                    <p><strong>{{ __('Usage Date:') }}</strong> {{ $rawMaterialUsage->usage_date ? $rawMaterialUsage->usage_date->format('M d, Y') : 'N/A' }}</p>
                                    <p><strong>{{ __('Usage Type:') }}</strong> 
                                        <span class="badge {{ $rawMaterialUsage->getUsageTypeBadgeClass() }}">
                                            {{ $rawMaterialUsage::getUsageTypes()[$rawMaterialUsage->usage_type] ?? $rawMaterialUsage->usage_type }}
                                        </span>
                                    </p>
                                    @if($rawMaterialUsage->batch_number)
                                        <p><strong>{{ __('Batch Number:') }}</strong> 
                                            <span class="badge bg-secondary">{{ $rawMaterialUsage->batch_number }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6>{{ __('Quantity & Cost Information') }}</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <h4 class="text-primary">{{ number_format($rawMaterialUsage->quantity_used, 3) }}</h4>
                                                    <small class="text-muted">{{ __('Quantity Used') }} ({{ $rawMaterialUsage->rawMaterial->unit }})</small>
                                                </div>
                                                <div class="col-md-1">
                                                    <h4>×</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4 class="text-info">${{ number_format($rawMaterialUsage->cost_per_unit, 2) }}</h4>
                                                    <small class="text-muted">{{ __('Cost per Unit') }}</small>
                                                </div>
                                                <div class="col-md-1">
                                                    <h4>=</h4>
                                                </div>
                                                <div class="col-md-4">
                                                    <h4 class="text-success">${{ number_format($rawMaterialUsage->total_cost, 2) }}</h4>
                                                    <small class="text-muted">{{ __('Total Cost') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($rawMaterialUsage->product || $rawMaterialUsage->order)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h6>{{ __('Associated Items') }}</h6>
                                        @if($rawMaterialUsage->product)
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-box text-info me-2"></i>
                                                    <div>
                                                        <strong>{{ __('Product:') }}</strong> {{ $rawMaterialUsage->product->name }}
                                                        <br><small class="text-muted">{{ __('This usage was recorded for product manufacturing') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($rawMaterialUsage->order)
                                            <div class="alert alert-warning">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                                    <div>
                                                        <strong>{{ __('Order:') }}</strong> #{{ $rawMaterialUsage->order->id }}
                                                        @if($rawMaterialUsage->order->customer)
                                                            - {{ $rawMaterialUsage->order->customer->name }}
                                                        @endif
                                                        <br><small class="text-muted">{{ __('Order Date:') }} {{ $rawMaterialUsage->order->order_date ? $rawMaterialUsage->order->order_date->format('M d, Y') : 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($rawMaterialUsage->notes)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6>{{ __('Notes') }}</h6>
                                        <div class="alert alert-secondary">
                                            <i class="fas fa-sticky-note me-2"></i>
                                            {{ $rawMaterialUsage->notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('Record Information') }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Recorded By:') }}</strong> {{ $rawMaterialUsage->recordedBy->name }}</p>
                            <p><strong>{{ __('Recorded On:') }}</strong> {{ $rawMaterialUsage->created_at->format('M d, Y H:i') }}</p>
                            @if($rawMaterialUsage->updated_at != $rawMaterialUsage->created_at)
                                <p><strong>{{ __('Last Updated:') }}</strong> {{ $rawMaterialUsage->updated_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('Quick Actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('raw-material-usages.edit', $rawMaterialUsage) }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> {{ __('Edit Usage') }}
                            </a>
                            <a href="{{ route('raw-materials.show', $rawMaterialUsage->rawMaterial) }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-cube"></i> {{ __('View Raw Material') }}
                            </a>
                            @if($rawMaterialUsage->product)
                                <a href="{{ route('products.show', $rawMaterialUsage->product) }}" class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-box"></i> {{ __('View Product') }}
                                </a>
                            @endif
                            @if($rawMaterialUsage->order)
                                <a href="{{ route('orders.show', $rawMaterialUsage->order) }}" class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-shopping-cart"></i> {{ __('View Order') }}
                                </a>
                            @endif
                            <form method="POST" action="{{ route('raw-material-usages.destroy', $rawMaterialUsage) }}" onsubmit="return confirm('{{ __('Are you sure? This will restore the raw material stock.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete Usage') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6>{{ __('Impact Summary') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">{{ __('Stock Reduced By:') }}</small>
                                <div class="fw-bold text-danger">
                                    -{{ number_format($rawMaterialUsage->quantity_used, 3) }} {{ $rawMaterialUsage->rawMaterial->unit }}
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('Cost Impact:') }}</small>
                                <div class="fw-bold text-warning">
                                    ${{ number_format($rawMaterialUsage->total_cost, 2) }}
                                </div>
                            </div>
                            @php
                                $remainingStock = $rawMaterialUsage->rawMaterial->quantity;
                                $minLevel = $rawMaterialUsage->rawMaterial->minimum_stock_level ?? 0;
                            @endphp
                            <div>
                                <small class="text-muted">{{ __('Current Stock Level:') }}</small>
                                <div class="fw-bold {{ $remainingStock <= $minLevel ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($remainingStock, 2) }} {{ $rawMaterialUsage->rawMaterial->unit }}
                                    @if($remainingStock <= $minLevel)
                                        <br><small class="text-danger">{{ __('Below minimum level!') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection