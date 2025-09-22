@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Raw Materials for: {{ $product->name }}</h4>
                    <div>
                        <a href="{{ route('products.raw-materials.create', $product) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Raw Materials
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Product Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Product Information</h6>
                                    <p><strong>Name:</strong> {{ $product->name }}</p>
                                    <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                                    <p><strong>Current Stock:</strong> {{ $product->quantity }} {{ $product->unit }}</p>
                                    <p><strong>Selling Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Cost Analysis</h6>
                                    <p><strong>Raw Material Cost:</strong> ${{ number_format($product->getTotalRawMaterialCost(), 2) }}</p>
                                    <p><strong>Current Product Cost:</strong> ${{ number_format($product->cost, 2) }}</p>
                                    <p><strong>Profit Margin:</strong> ${{ number_format($product->price - $product->getTotalRawMaterialCost(), 2) }}</p>
                                    <p><strong>Raw Materials Used:</strong> {{ $product->rawMaterials->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($product->rawMaterials->count() > 0)
                        <!-- Raw Materials Table -->
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
                                        <th>Type</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->rawMaterials as $rawMaterial)
                                        <tr>
                                            <td>
                                                <strong>{{ $rawMaterial->name }}</strong>
                                                <br>
                                                <small class="text-muted">Stock: {{ $rawMaterial->quantity }} {{ $rawMaterial->unit }}</small>
                                            </td>
                                            <td>{{ $rawMaterial->pivot->quantity_required }}</td>
                                            <td>{{ $rawMaterial->pivot->unit }}</td>
                                            <td>${{ number_format($rawMaterial->pivot->cost_per_unit ?? $rawMaterial->cost_per_unit, 2) }}</td>
                                            <td>
                                                @php
                                                    $costPerUnit = $rawMaterial->pivot->cost_per_unit ?? $rawMaterial->cost_per_unit;
                                                    $quantityWithWaste = $rawMaterial->pivot->quantity_required * (1 + ($rawMaterial->pivot->waste_percentage / 100));
                                                    $totalCost = $quantityWithWaste * $costPerUnit;
                                                @endphp
                                                ${{ number_format($totalCost, 2) }}
                                            </td>
                                            <td>{{ $rawMaterial->pivot->waste_percentage }}%</td>
                                            <td>
                                                @if($rawMaterial->pivot->is_primary)
                                                    <span class="badge bg-primary">Primary</span>
                                                @else
                                                    <span class="badge bg-secondary">Secondary</span>
                                                @endif
                                            </td>
                                            <td>{{ $rawMaterial->pivot->notes ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.raw-materials.edit', [$product, $rawMaterial]) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('products.raw-materials.destroy', [$product, $rawMaterial]) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Remove this raw material from the product?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-info">
                                    <tr>
                                        <th colspan="4">Total Raw Material Cost</th>
                                        <th>${{ number_format($product->getTotalRawMaterialCost(), 2) }}</th>
                                        <th colspan="4"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>No Raw Materials Added</h5>
                            <p class="text-muted">This product doesn't have any raw materials defined yet.</p>
                            <a href="{{ route('products.raw-materials.create', $product) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Raw Materials
                            </a>
                        </div>
                    @endif

                    @if($availableRawMaterials->count() > 0)
                        <!-- Quick Add Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>Quick Add Available Raw Materials</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('products.raw-materials.bulk-add', $product) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Select Raw Materials</label>
                                            <select name="raw_material_ids[]" class="form-control" multiple size="5">
                                                @foreach($availableRawMaterials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->name }} ({{ $material->unit }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Default Quantity</label>
                                                    <input type="number" name="default_quantity" class="form-control" 
                                                           step="0.001" value="1" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Default Unit</label>
                                                    <input type="text" name="default_unit" class="form-control" 
                                                           value="kg" required>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <label>Default Waste %</label>
                                                <input type="number" name="default_waste_percentage" class="form-control" 
                                                       step="0.01" value="0" min="0" max="100">
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Add Selected Materials
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cost Calculator Modal -->
<div class="modal fade" id="costCalculatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Production Cost Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label>Production Quantity</label>
                    <input type="number" id="calc_quantity" class="form-control" value="1" step="0.001">
                </div>
                <button type="button" id="calculate-cost" class="btn btn-primary">Calculate</button>
                
                <div id="cost-results" class="mt-3" style="display: none;">
                    <div class="bg-light p-3 rounded">
                        <p><strong>Quantity:</strong> <span id="result-quantity"></span></p>
                        <p><strong>Total Raw Material Cost:</strong> $<span id="result-total-cost"></span></p>
                        <p><strong>Cost Per Unit:</strong> $<span id="result-cost-per-unit"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('calculate-cost')?.addEventListener('click', function() {
    const quantity = document.getElementById('calc_quantity').value;
    
    fetch(`{{ route('products.calculate-cost', $product) }}?quantity=${quantity}`)
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