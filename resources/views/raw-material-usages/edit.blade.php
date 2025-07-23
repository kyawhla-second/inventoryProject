@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Raw Material Usage') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('raw-material-usages.update', $rawMaterialUsage) }}" id="usageForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="raw_material_id" class="form-label">{{ __('Raw Material') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('raw_material_id') is-invalid @enderror" 
                                            id="raw_material_id" name="raw_material_id" required onchange="updateMaterialInfo()">
                                        <option value="">{{ __('Select Raw Material') }}</option>
                                        @foreach($rawMaterials as $material)
                                            <option value="{{ $material->id }}" 
                                                    data-unit="{{ $material->unit }}"
                                                    data-cost="{{ $material->cost_per_unit }}"
                                                    data-available="{{ $material->quantity + ($material->id == $rawMaterialUsage->raw_material_id ? $rawMaterialUsage->quantity_used : 0) }}"
                                                    {{ old('raw_material_id', $rawMaterialUsage->raw_material_id) == $material->id ? 'selected' : '' }}>
                                                {{ $material->name }} ({{ __('Available:') }} {{ $material->quantity + ($material->id == $rawMaterialUsage->raw_material_id ? $rawMaterialUsage->quantity_used : 0) }} {{ $material->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('raw_material_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usage_date" class="form-label">{{ __('Usage Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('usage_date') is-invalid @enderror" 
                                           id="usage_date" name="usage_date" 
                                           value="{{ old('usage_date', $rawMaterialUsage->usage_date ? $rawMaterialUsage->usage_date->format('Y-m-d') : '') }}" required>
                                    @error('usage_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity_used" class="form-label">{{ __('Quantity Used') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" class="form-control @error('quantity_used') is-invalid @enderror" 
                                               id="quantity_used" name="quantity_used" 
                                               value="{{ old('quantity_used', $rawMaterialUsage->quantity_used) }}" min="0.001" required onchange="calculateTotalCost()">
                                        <span class="input-group-text" id="unit-display">{{ $rawMaterialUsage->rawMaterial->unit }}</span>
                                    </div>
                                    @error('quantity_used')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="available-quantity">{{ __('Available:') }} {{ $rawMaterialUsage->rawMaterial->quantity + $rawMaterialUsage->quantity_used }} {{ $rawMaterialUsage->rawMaterial->unit }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="usage_type" class="form-label">{{ __('Usage Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('usage_type') is-invalid @enderror" 
                                            id="usage_type" name="usage_type" required>
                                        <option value="">{{ __('Select Usage Type') }}</option>
                                        @foreach($usageTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('usage_type', $rawMaterialUsage->usage_type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('usage_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="batch_number" class="form-label">{{ __('Batch Number') }}</label>
                                    <input type="text" class="form-control @error('batch_number') is-invalid @enderror" 
                                           id="batch_number" name="batch_number" 
                                           value="{{ old('batch_number', $rawMaterialUsage->batch_number) }}" maxlength="100">
                                    @error('batch_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">{{ __('Associated Product') }}</label>
                                    <select class="form-control @error('product_id') is-invalid @enderror" 
                                            id="product_id" name="product_id">
                                        <option value="">{{ __('Select Product (Optional)') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id', $rawMaterialUsage->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Select if this usage is for a specific product') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">{{ __('Associated Order') }}</label>
                                    <select class="form-control @error('order_id') is-invalid @enderror" 
                                            id="order_id" name="order_id">
                                        <option value="">{{ __('Select Order (Optional)') }}</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}" {{ old('order_id', $rawMaterialUsage->order_id) == $order->id ? 'selected' : '' }}>
                                                Order #{{ $order->id }} - {{ $order->customer->name ?? 'No Customer' }}
                                                ({{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Select if this usage is for a specific order') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" maxlength="1000">{{ old('notes', $rawMaterialUsage->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Additional notes about this usage (optional)') }}</div>
                        </div>

                        <!-- Cost Information -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">{{ __('Cost Information') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('Cost per Unit') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" id="cost-per-unit-display" 
                                                       value="{{ number_format($rawMaterialUsage->cost_per_unit, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('Quantity Used') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="quantity-display" 
                                                       value="{{ number_format($rawMaterialUsage->quantity_used, 3) }}" readonly>
                                                <span class="input-group-text" id="unit-display-2">{{ $rawMaterialUsage->rawMaterial->unit }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('Total Cost') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control fw-bold text-success" id="total-cost-display" 
                                                       value="{{ number_format($rawMaterialUsage->total_cost, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ __('Note:') }}</strong> {{ __('Changing the raw material or quantity will affect stock levels. The system will automatically adjust the inventory.') }}
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('raw-material-usages.show', $rawMaterialUsage) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update Usage') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateMaterialInfo() {
    const select = document.getElementById('raw_material_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const unit = selectedOption.dataset.unit;
        const cost = parseFloat(selectedOption.dataset.cost);
        const available = parseFloat(selectedOption.dataset.available);
        
        // Update unit displays
        document.getElementById('unit-display').textContent = unit;
        document.getElementById('unit-display-2').textContent = unit;
        
        // Update cost display
        document.getElementById('cost-per-unit-display').value = cost.toFixed(2);
        
        // Update available quantity
        document.getElementById('available-quantity').innerHTML = 
            `<span class="text-info"><i class="fas fa-info-circle"></i> Available: ${available} ${unit}</span>`;
        
        // Update quantity input max
        document.getElementById('quantity_used').max = available;
        
        calculateTotalCost();
    } else {
        // Reset displays
        document.getElementById('unit-display').textContent = 'Unit';
        document.getElementById('unit-display-2').textContent = 'Unit';
        document.getElementById('cost-per-unit-display').value = '';
        document.getElementById('available-quantity').textContent = 'Available quantity will be shown here';
        document.getElementById('quantity-display').value = '';
        document.getElementById('total-cost-display').value = '';
    }
}

function calculateTotalCost() {
    const quantity = parseFloat(document.getElementById('quantity_used').value) || 0;
    const costPerUnit = parseFloat(document.getElementById('cost-per-unit-display').value) || 0;
    const totalCost = quantity * costPerUnit;
    
    document.getElementById('quantity-display').value = quantity.toFixed(3);
    document.getElementById('total-cost-display').value = totalCost.toFixed(2);
    
    // Check if quantity exceeds available
    const select = document.getElementById('raw_material_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const available = parseFloat(selectedOption.dataset.available);
        const quantityInput = document.getElementById('quantity_used');
        
        if (quantity > available) {
            quantityInput.classList.add('is-invalid');
            if (!document.getElementById('quantity-error')) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'quantity-error';
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = `Quantity exceeds available stock (${available} ${selectedOption.dataset.unit})`;
                quantityInput.parentNode.appendChild(errorDiv);
            }
        } else {
            quantityInput.classList.remove('is-invalid');
            const errorDiv = document.getElementById('quantity-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotalCost();
    
    // Add event listeners
    document.getElementById('quantity_used').addEventListener('input', calculateTotalCost);
    document.getElementById('raw_material_id').addEventListener('change', updateMaterialInfo);
    
    // Prevent form submission if quantity exceeds available
    document.getElementById('usageForm').addEventListener('submit', function(e) {
        const quantityInput = document.getElementById('quantity_used');
        if (quantityInput.classList.contains('is-invalid')) {
            e.preventDefault();
            alert('Please correct the quantity before submitting.');
        }
    });
});
</script>

<style>
.card-title {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    font-weight: bold;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.fw-bold {
    font-weight: bold !important;
}

.text-success {
    color: #198754 !important;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>
@endsection