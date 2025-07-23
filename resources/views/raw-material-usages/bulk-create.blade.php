@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Bulk Record Raw Material Usage') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('raw-material-usages.bulk-store') }}" id="bulkUsageForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="common_usage_date" class="form-label">{{ __('Usage Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('common_usage_date') is-invalid @enderror" 
                                           id="common_usage_date" name="common_usage_date" 
                                           value="{{ old('common_usage_date', date('Y-m-d')) }}" required>
                                    @error('common_usage_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="common_product_id" class="form-label">{{ __('Associated Product (Optional)') }}</label>
                                    <select class="form-control @error('common_product_id') is-invalid @enderror" 
                                            id="common_product_id" name="common_product_id">
                                        <option value="">{{ __('Select Product (Optional)') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('common_product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('common_product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="common_order_id" class="form-label">{{ __('Associated Order (Optional)') }}</label>
                                    <select class="form-control @error('common_order_id') is-invalid @enderror" 
                                            id="common_order_id" name="common_order_id">
                                        <option value="">{{ __('Select Order (Optional)') }}</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}" {{ old('common_order_id') == $order->id ? 'selected' : '' }}>
                                                Order #{{ $order->id }} - {{ $order->customer->name ?? 'No Customer' }}
                                                ({{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('common_order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>{{ __('Raw Materials Usage') }}</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="addMaterialRow()">
                                <i class="fas fa-plus"></i> {{ __('Add Material') }}
                            </button>
                        </div>

                        <div id="materials-container">
                            <!-- Initial material row -->
                            <div class="material-row border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Raw Material') }} <span class="text-danger">*</span></label>
                                        <select class="form-control raw-material-select" name="usages[0][raw_material_id]" required onchange="updateMaterialInfo(this, 0)">
                                            <option value="">{{ __('Select Raw Material') }}</option>
                                            @foreach($rawMaterials as $material)
                                                <option value="{{ $material->id }}" 
                                                        data-unit="{{ $material->unit }}"
                                                        data-cost="{{ $material->cost_per_unit }}"
                                                        data-available="{{ $material->quantity }}">
                                                    {{ $material->name }} ({{ __('Available:') }} {{ $material->quantity }} {{ $material->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Quantity Used') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="0.001" class="form-control quantity-input" 
                                                   name="usages[0][quantity_used]" value="0" min="0.001" required onchange="calculateItemCost(0)">
                                            <span class="input-group-text unit-display">{{ __('Unit') }}</span>
                                        </div>
                                        <small class="text-muted available-quantity">{{ __('Available quantity will be shown here') }}</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Usage Type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="usages[0][usage_type]" required>
                                            <option value="">{{ __('Select Type') }}</option>
                                            @foreach($usageTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Batch Number') }}</label>
                                        <input type="text" class="form-control" name="usages[0][batch_number]" maxlength="100">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Cost') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control cost-display" readonly value="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">
                                            <i class="fas fa-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <table class="table table-sm mb-0">
                                            <tr class="fw-bold">
                                                <td>{{ __('TOTAL COST:') }}</td>
                                                <td class="text-end">$<span id="total-cost">0.00</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('raw-material-usages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Record Bulk Usage') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let materialIndex = 1;
let rawMaterialsData = @json($rawMaterials);
let usageTypesData = @json($usageTypes);

function addMaterialRow() {
    const container = document.getElementById('materials-container');
    
    // Create the new row element
    const newRow = document.createElement('div');
    newRow.className = 'material-row border rounded p-3 mb-3';
    
    // Build raw materials options
    let materialOptions = '<option value="">{{ __("Select Raw Material") }}</option>';
    rawMaterialsData.forEach(material => {
        materialOptions += `<option value="${material.id}" 
                                   data-unit="${material.unit}"
                                   data-cost="${material.cost_per_unit}"
                                   data-available="${material.quantity}">
                               ${material.name} ({{ __("Available:") }} ${material.quantity} ${material.unit})
                           </option>`;
    });
    
    // Build usage type options
    let usageTypeOptions = '<option value="">{{ __("Select Type") }}</option>';
    Object.entries(usageTypesData).forEach(([key, label]) => {
        usageTypeOptions += `<option value="${key}">${label}</option>`;
    });
    
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">{{ __('Raw Material') }} <span class="text-danger">*</span></label>
                <select class="form-control raw-material-select" name="usages[${materialIndex}][raw_material_id]" required onchange="updateMaterialInfo(this, ${materialIndex})">
                    ${materialOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Quantity Used') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.001" class="form-control quantity-input" 
                           name="usages[${materialIndex}][quantity_used]" value="0" min="0.001" required onchange="calculateItemCost(${materialIndex})">
                    <span class="input-group-text unit-display">{{ __('Unit') }}</span>
                </div>
                <small class="text-muted available-quantity">{{ __('Available quantity will be shown here') }}</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Usage Type') }} <span class="text-danger">*</span></label>
                <select class="form-control" name="usages[${materialIndex}][usage_type]" required>
                    ${usageTypeOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Batch Number') }}</label>
                <input type="text" class="form-control" name="usages[${materialIndex}][batch_number]" maxlength="100">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Cost') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control cost-display" readonly value="0.00">
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeMaterialRow(this)">
                    <i class="fas fa-trash"></i> {{ __('Remove') }}
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    materialIndex++;
    calculateTotalCost();
}

function removeMaterialRow(button) {
    const materialRow = button.closest('.material-row');
    materialRow.remove();
    calculateTotalCost();
}

function updateMaterialInfo(select, index) {
    const row = select.closest('.material-row');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const unit = selectedOption.dataset.unit || 'Unit';
        const cost = parseFloat(selectedOption.dataset.cost) || 0;
        const available = parseFloat(selectedOption.dataset.available) || 0;
        
        // Update unit display
        const unitDisplay = row.querySelector('.unit-display');
        if (unitDisplay) unitDisplay.textContent = unit;
        
        // Update available quantity
        const availableQuantity = row.querySelector('.available-quantity');
        if (availableQuantity) {
            availableQuantity.innerHTML = 
                `<span class="text-info"><i class="fas fa-info-circle"></i> {{ __('Available:') }} ${available.toFixed(2)} ${unit}</span>`;
        }
        
        // Set max quantity
        const quantityInput = row.querySelector('.quantity-input');
        if (quantityInput) {
            quantityInput.max = available;
            calculateItemCost(index);
        }
    }
}

function calculateItemCost(index) {
    const rows = document.querySelectorAll('.material-row');
    const row = rows[index];
    if (!row) return;
    
    const select = row.querySelector('.raw-material-select');
    const quantityInput = row.querySelector('.quantity-input');
    const costDisplay = row.querySelector('.cost-display');
    
    if (select && quantityInput && costDisplay) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const cost = parseFloat(selectedOption.dataset.cost) || 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            const totalCost = cost * quantity;
            
            costDisplay.value = totalCost.toFixed(2);
            
            // Validate quantity
            const available = parseFloat(selectedOption.dataset.available) || 0;
            if (quantity > available) {
                quantityInput.classList.add('is-invalid');
                if (!row.querySelector('.invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = `{{ __('Quantity exceeds available stock') }} (${available.toFixed(2)} ${selectedOption.dataset.unit || 'units'})`;
                    quantityInput.parentNode.appendChild(errorDiv);
                }
            } else {
                quantityInput.classList.remove('is-invalid');
                const errorDiv = row.querySelector('.invalid-feedback');
                if (errorDiv) errorDiv.remove();
            }
            
            calculateTotalCost();
        }
    }
}

function calculateTotalCost() {
    let totalCost = 0;
    document.querySelectorAll('.cost-display').forEach(input => {
        totalCost += parseFloat(input.value) || 0;
    });
    document.getElementById('total-cost').textContent = totalCost.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize first row
    const firstSelect = document.querySelector('.raw-material-select');
    if (firstSelect) updateMaterialInfo(firstSelect, 0);
    
    // Form validation
    document.getElementById('bulkUsageForm').addEventListener('submit', function(e) {
        let hasErrors = false;
        document.querySelectorAll('.quantity-input').forEach(input => {
            if (input.classList.contains('is-invalid')) {
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('{{ __("Please correct the quantity errors before submitting.") }}');
        }
    });
});
</script>

<style>
.material-row {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6 !important;
    transition: all 0.3s ease;
}

.material-row:hover {
    background-color: #e9ecef;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    font-weight: bold;
}

.input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>

@endsection