@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add Raw Materials to: {{ $product->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.raw-materials.store', $product) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Product Information</h6>
                                        <p><strong>Name:</strong> {{ $product->name }}</p>
                                        <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                                        <p><strong>Current Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                        <p><strong>Current Cost:</strong> ${{ number_format($product->cost, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Instructions</h6>
                                        <ul class="mb-0">
                                            <li>Add all raw materials needed to create this product</li>
                                            <li>Specify exact quantities required per unit of product</li>
                                            <li>Include waste percentage for accurate costing</li>
                                            <li>Mark primary ingredients for better organization</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Raw Materials Section -->
                        <div class="card">
                            <div class="card-header">
                                <h5>Raw Materials Required</h5>
                            </div>
                            <div class="card-body">
                                @if($rawMaterials->count() > 0)
                                    <div id="raw-materials">
                                        <div class="raw-material-item border rounded p-3 mb-3">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Raw Material *</label>
                                                    <select name="raw_materials[0][raw_material_id]" class="form-control raw-material-select" required>
                                                        <option value="">Select Raw Material</option>
                                                        @foreach($rawMaterials as $material)
                                                            <option value="{{ $material->id }}" 
                                                                    data-unit="{{ $material->unit }}" 
                                                                    data-cost="{{ $material->cost_per_unit }}">
                                                                {{ $material->name }} ({{ $material->unit }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            <div class="col-md-2">
                                                <label>Quantity Required *</label>
                                                <input type="number" name="raw_materials[0][quantity_required]" 
                                                       class="form-control quantity-input" step="0.001" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Unit *</label>
                                                <input type="text" name="raw_materials[0][unit]" 
                                                       class="form-control unit-input" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Cost/Unit</label>
                                                <input type="number" name="raw_materials[0][cost_per_unit]" 
                                                       class="form-control cost-input" step="0.01" 
                                                       placeholder="Auto-filled">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Waste %</label>
                                                <input type="number" name="raw_materials[0][waste_percentage]" 
                                                       class="form-control" step="0.01" value="0" min="0" max="100">
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-item d-block">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" name="raw_materials[0][is_primary]" 
                                                           class="form-check-input" value="1">
                                                    <label class="form-check-label">Primary Ingredient</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="raw_materials[0][notes]" 
                                                       class="form-control" placeholder="Notes (optional)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    </div>
                                    
                                    <button type="button" id="add-material" class="btn btn-secondary">
                                        <i class="fas fa-plus"></i> Add Another Raw Material
                                    </button>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                                        <h5>No Available Raw Materials</h5>
                                        <p class="text-muted">All available raw materials have already been added to this product.</p>
                                        <a href="{{ route('products.raw-materials.index', $product) }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-left"></i> Back to Raw Materials
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($rawMaterials->count() > 0)
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Raw Materials
                                </button>
                                <a href="{{ route('products.raw-materials.index', $product) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let materialIndex = 1;
    
    // Add new raw material row
    document.getElementById('add-material').addEventListener('click', function() {
        const container = document.getElementById('raw-materials');
        const newItem = document.querySelector('.raw-material-item').cloneNode(true);
        
        // Update input names and clear values
        newItem.querySelectorAll('input, select').forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${materialIndex}]`);
            input.name = name;
            if (input.type !== 'checkbox') {
                input.value = '';
            } else {
                input.checked = false;
            }
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        container.appendChild(newItem);
        materialIndex++;
    });
    
    // Remove raw material row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            if (document.querySelectorAll('.raw-material-item').length > 1) {
                e.target.closest('.raw-material-item').remove();
            } else {
                alert('At least one raw material is required.');
            }
        }
    });
    
    // Auto-fill unit and cost when raw material is selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('raw-material-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const container = e.target.closest('.raw-material-item');
            
            if (selectedOption.value) {
                const unit = selectedOption.getAttribute('data-unit');
                const cost = selectedOption.getAttribute('data-cost');
                
                container.querySelector('.unit-input').value = unit;
                container.querySelector('.cost-input').value = cost;
            }
        }
    });
});
</script>
@endsection