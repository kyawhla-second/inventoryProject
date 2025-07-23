@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Create Purchase Order from Customer Order') }} #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> {{ __('Customer Order Information') }}</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ __('Customer:') }}</strong> {{ $order->customer->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ __('Order Date:') }}</strong> {{ $order->order_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ __('Total Amount:') }}</strong> ${{ number_format($order->total_amount, 2) }}
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('orders.create-purchase', $order) }}" id="purchaseForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">{{ __('Supplier') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" name="supplier_id" required>
                                        <option value="">{{ __('Select Supplier') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                                @if($supplier->email)
                                                    ({{ $supplier->email }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Purchase Date') }}</label>
                                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                                    <div class="form-text">{{ __('Purchase order will be created with today\'s date') }}</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>{{ __('Purchase Items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="addItem()">
                                <i class="fas fa-plus"></i> {{ __('Add Item') }}
                            </button>
                        </div>

                        <div id="items-container">
                            <!-- Initial item row -->
                            <div class="item-row border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Product') }}</label>
                                        <select class="form-control" name="items[0][product_id]" onchange="updateProductInfo(this, 0)">
                                            <option value="">{{ __('Select Product (Optional)') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="items[0][description]" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="items[0][quantity]" 
                                               value="1.00" min="0.01" required onchange="calculateItemTotal(0)">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" name="items[0][price]" 
                                                   value="0.00" min="0" required onchange="calculateItemTotal(0)">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-10">
                                        <small class="text-muted">{{ __('Item Total:') }} $<span id="item-total-0">0.00</span></small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Summary -->
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <table class="table table-sm mb-0">
                                            <tr class="fw-bold">
                                                <td>{{ __('TOTAL PURCHASE AMOUNT:') }}</td>
                                                <td class="text-end">$<span id="total-amount">0.00</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Create Purchase Order') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Product') }}</label>
                    <select class="form-control" name="items[${itemIndex}][product_id]" onchange="updateProductInfo(this, ${itemIndex})">
                        <option value="">{{ __('Select Product (Optional)') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-name="{{ $product->name }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${itemIndex}][description]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="items[${itemIndex}][quantity]" 
                           value="1.00" min="0.01" required onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="items[${itemIndex}][price]" 
                               value="0.00" min="0" required onchange="calculateItemTotal(${itemIndex})">
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-10">
                    <small class="text-muted">{{ __('Item Total:') }} $<span id="item-total-${itemIndex}">0.00</span></small>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
    calculateTotalAmount();
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    itemRow.remove();
    calculateTotalAmount();
}

function updateProductInfo(select, index) {
    const option = select.options[select.selectedIndex];
    const descriptionInput = document.querySelector(`input[name="items[${index}][description]"]`);
    
    if (option.value && option.dataset.name) {
        descriptionInput.value = option.dataset.name;
    }
}

function calculateItemTotal(index) {
    const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][price]"]`);
    const totalSpan = document.getElementById(`item-total-${index}`);
    
    if (quantityInput && priceInput && totalSpan) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        totalSpan.textContent = total.toFixed(2);
        calculateTotalAmount();
    }
}

function calculateTotalAmount() {
    let total = 0;
    
    document.querySelectorAll('[id^="item-total-"]').forEach(span => {
        total += parseFloat(span.textContent) || 0;
    });
    
    document.getElementById('total-amount').textContent = total.toFixed(2);
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateItemTotal(0);
});
</script>

<style>
.item-row {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
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

.bg-light {
    background-color: #f8f9fa !important;
}

.fw-bold {
    font-weight: bold !important;
}
</style>
@endsection