@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $recipe->name }}</h4>
                    <div>
                        <span class="badge {{ $recipe->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $recipe->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Product Information</h6>
                            <p><strong>Product:</strong> {{ $recipe->product->name }}</p>
                            <p><strong>Version:</strong> {{ $recipe->version }}</p>
                            <p><strong>Batch Size:</strong> {{ $recipe->batch_size }} {{ $recipe->unit }}</p>
                            <p><strong>Yield:</strong> {{ $recipe->yield_percentage }}%</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Timing Information</h6>
                            <p><strong>Preparation Time:</strong> {{ $recipe->preparation_time ?? 'N/A' }} minutes</p>
                            <p><strong>Production Time:</strong> {{ $recipe->production_time ?? 'N/A' }} minutes</p>
                            <p><strong>Created By:</strong> {{ $recipe->createdBy->name }}</p>
                            <p><strong>Created:</strong> {{ $recipe->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    @if($recipe->description)
                        <div class="mb-4">
                            <h6>Description</h6>
                            <p>{{ $recipe->description }}</p>
                        </div>
                    @endif

                    @if($recipe->instructions)
                        <div class="mb-4">
                            <h6>Instructions</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($recipe->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    <h6>Recipe Items</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Raw Material</th>
                                    <th>Quantity Required</th>
                                    <th>Unit</th>
                                    <th>Cost/Unit</th>
                                    <th>Total Cost</th>
                                    <th>Waste %</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recipe->recipeItems as $item)
                                    <tr>
                                        <td>{{ $item->rawMaterial->name }}</td>
                                        <td>{{ $item->quantity_required }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>${{ number_format($item->cost_per_unit ?? $item->rawMaterial->cost_per_unit, 2) }}</td>
                                        <td>${{ number_format($item->getTotalCost(), 2) }}</td>
                                        <td>{{ $item->waste_percentage }}%</td>
                                        <td>{{ $item->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="4">Total Material Cost</th>
                                    <th>${{ number_format($recipe->getTotalMaterialCost(), 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="4">Cost Per Unit ({{ $recipe->unit }})</th>
                                    <th>${{ number_format($recipe->getEstimatedCostPerUnit(), 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Recipe
                        </a>
                        <form action="{{ route('recipes.duplicate', $recipe) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-copy"></i> Duplicate Recipe
                            </button>
                        </form>
                        <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Cost Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="calc_quantity">Production Quantity</label>
                        <input type="number" id="calc_quantity" class="form-control" 
                               value="{{ $recipe->batch_size }}" step="0.001">
                    </div>
                    <button type="button" id="calculate-cost" class="btn btn-primary btn-sm">Calculate</button>
                    
                    <div id="cost-results" class="mt-3" style="display: none;">
                        <div class="bg-light p-3 rounded">
                            <p><strong>Quantity:</strong> <span id="result-quantity"></span> {{ $recipe->unit }}</p>
                            <p><strong>Total Cost:</strong> $<span id="result-total-cost"></span></p>
                            <p><strong>Cost Per Unit:</strong> $<span id="result-cost-per-unit"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('calculate-cost').addEventListener('click', function() {
    const quantity = document.getElementById('calc_quantity').value;
    
    fetch(`{{ route('recipes.calculate-cost', $recipe) }}?quantity=${quantity}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('result-quantity').textContent = data.quantity;
            document.getElementById('result-total-cost').textContent = data.total_cost.toFixed(2);
            document.getElementById('result-cost-per-unit').textContent = data.cost_per_unit.toFixed(2);
            document.getElementById('cost-results').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error calculating cost');
        });
});
</script>
@endsection