@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Record Raw Material Usage') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('raw-material-usages.store') }}" id="usageForm">
                        @csrf

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
                                                    data-available="{{ $material->quantity }}"
                                                    {{ old('raw_material_id', $selectedRawMaterial) == $material->id ? 'selected' : '' }}>
                                                {{ $material->name }} ({{ __('Available:') }} {{ $material->quantity }} {{ $material->unit }})
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
                                           value="{{ old('usage_date', date('Y-m-d')) }}" required>
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
                                               value="{{ old('quantity_used') }}" min="0.001" required onchange="calculateTotalCost()">
                                        <span class="input-group-text" id="unit-display">{{ __('Unit') }}</span>
                                    </div>
                                    @error('quantity_used')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="available-quantity">{{ __('Available quantity will be shown here') }}</span>
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
                                            <option value="{{ $key }}" {{ old('usage_type') == $key ? 'selected' : '' }}>
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
                                           value="{{ old('batch_number') }}" maxlength="100">
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
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
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
                                            <option value="{{ $order->id }}" {{ old('order_id', $selectedOrder) == $order->id ? 'selected' : '' }}>
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
                                      id="notes" name="notes" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
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
                                                <input type="text" class="form-control" id="cost-per-unit-display" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('Quantity Used') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="quantity-display" readonly>
                                                <span class="input-group-text" id="unit-display-2">{{ __('Unit') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('Total Cost') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control fw-bold text-success" id="total-cost-display" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('raw-material-usages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Record Usage') }}
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
    
    console.log('updateMaterialInfo called', selectedOption); // Debug log
    
    if (selectedOption && selectedOption.value) {
        const unit = selectedOption.dataset.unit || 'Unit';
        const cost = parseFloat(selectedOption.dataset.cost) || 0;
        const available = parseFloat(selectedOption.dataset.available) || 0;
        
        console.log('Material data:', { unit, cost, available }); // Debug log
        
        // Update unit displays
        const unitDisplay = document.getElementById('unit-display');
        const unitDisplay2 = document.getElementById('unit-display-2');
        if (unitDisplay) unitDisplay.textContent = unit;
        if (unitDisplay2) unitDisplay2.textContent = unit;
        
        // Update cost display
        const costDisplay = document.getElementById('cost-per-unit-display');
        if (costDisplay) {
            costDisplay.value = cost.toFixed(2);
            console.log('Cost display updated:', cost.toFixed(2)); // Debug log
        }
        
        // Update available quantity
        const availableQuantity = document.getElementById('available-quantity');
        if (availableQuantity) {
            availableQuantity.innerHTML = 
                `<span class="text-info"><i class="fas fa-info-circle"></i> Available: ${available.toFixed(2)} ${unit}</span>`;
        }
        
        // Update quantity input max
        const quantityInput = document.getElementById('quantity_used');
        if (quantityInput) {
            quantityInput.max = available;
        }
        
        calculateTotalCost();
    } else {
        // Reset displays
        resetDisplays();
    }
}

function resetDisplays() {
    const elements = {
        'unit-display': 'Unit',
        'unit-display-2': 'Unit',
        'cost-per-unit-display': '',
        'available-quantity': 'Available quantity will be shown here',
        'quantity-display': '',
        'total-cost-display': ''
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (id === 'available-quantity') {
                element.textContent = elements[id];
            } else if (id.includes('unit-display')) {
                element.textContent = elements[id];
            } else {
                element.value = elements[id];
            }
        }
    });
}

function calculateTotalCost() {
    const quantityInput = document.getElementById('quantity_used');
    const costDisplay = document.getElementById('cost-per-unit-display');
    const quantityDisplay = document.getElementById('quantity-display');
    const totalCostDisplay = document.getElementById('total-cost-display');
    
    if (!quantityInput || !costDisplay || !quantityDisplay || !totalCostDisplay) {
        console.error('Required elements not found for calculation');
        return;
    }
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const costPerUnit = parseFloat(costDisplay.value) || 0;
    const totalCost = quantity * costPerUnit;
    
    console.log('Calculating:', { quantity, costPerUnit, totalCost }); // Debug log
    
    quantityDisplay.value = quantity.toFixed(3);
    totalCostDisplay.value = totalCost.toFixed(2);
    
    // Check if quantity exceeds available
    validateQuantity();
}

function validateQuantity() {
    const select = document.getElementById('raw_material_id');
    const quantityInput = document.getElementById('quantity_used');
    
    if (!select || !quantityInput) return;
    
    const selectedOption = select.options[select.selectedIndex];
    const quantity = parseFloat(quantityInput.value) || 0;
    
    if (selectedOption && selectedOption.value) {
        const available = parseFloat(selectedOption.dataset.available) || 0;
        
        // Remove existing error
        const existingError = document.getElementById('quantity-error');
        if (existingError) {
            existingError.remove();
        }
        quantityInput.classList.remove('is-invalid');
        
        if (quantity > available) {
            quantityInput.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.id = 'quantity-error';
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = `Quantity exceeds available stock (${available.toFixed(2)} ${selectedOption.dataset.unit || 'units'})`;
            quantityInput.parentNode.appendChild(errorDiv);
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...'); // Debug log
    
    // Initialize displays
    updateMaterialInfo();
    
    // Add event listeners
    const rawMaterialSelect = document.getElementById('raw_material_id');
    const quantityInput = document.getElementById('quantity_used');
    const usageForm = document.getElementById('usageForm');
    
    if (rawMaterialSelect) {
        rawMaterialSelect.addEventListener('change', updateMaterialInfo);
        console.log('Raw material select listener added'); // Debug log
    }
    
    if (quantityInput) {
        quantityInput.addEventListener('input', calculateTotalCost);
        quantityInput.addEventListener('change', calculateTotalCost);
        console.log('Quantity input listeners added'); // Debug log
    }
    
    // Prevent form submission if quantity exceeds available
    if (usageForm) {
        usageForm.addEventListener('submit', function(e) {
            const quantityInput = document.getElementById('quantity_used');
            if (quantityInput && quantityInput.classList.contains('is-invalid')) {
                e.preventDefault();
                alert('Please correct the quantity before submitting.');
                return false;
            }
        });
    }
    
    console.log('Initialization complete'); // Debug log
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