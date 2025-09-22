@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Create Invoice') }}</h4>
                    @if($sale)
                        <small class="text-muted">{{ __('Creating from Sale #') }}{{ $sale->id }}</small>
                    @endif
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                        @csrf
                        
                        @if($sale)
                            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">{{ __('Customer') }}</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                        <option value="">{{ __('Walk-in Customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                {{ old('customer_id', $sale ? $sale->customer_id : '') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('Invoice Date') }}</label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                           id="invoice_date" name="invoice_date" 
                                           value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">{{ __('Due Date') }}</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" 
                                           value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_terms" class="form-label">{{ __('Payment Terms') }}</label>
                                    <select class="form-control" id="payment_terms" name="payment_terms">
                                        <option value="Net 30" {{ old('payment_terms') == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                                        <option value="Net 15" {{ old('payment_terms') == 'Net 15' ? 'selected' : '' }}>Net 15</option>
                                        <option value="Due on Receipt" {{ old('payment_terms') == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                        <option value="Cash on Delivery" {{ old('payment_terms') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">{{ __('Tax Rate (%)') }}</label>
                                    <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror" 
                                           id="tax_rate" name="tax_rate" value="{{ old('tax_rate', '0.00') }}" min="0" max="100">
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_rate" class="form-label">{{ __('Discount Rate (%)') }}</label>
                                    <input type="number" step="0.01" class="form-control @error('discount_rate') is-invalid @enderror" 
                                           id="discount_rate" name="discount_rate" value="{{ old('discount_rate', '0.00') }}" min="0" max="100">
                                    @error('discount_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>{{ __('Invoice Items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="addItem()">
                                <i class="fas fa-plus"></i> {{ __('Add Item') }}
                            </button>
                        </div>

                        <div id="items-container">
                            @if($sale && $sale->items->count() > 0)
                                @foreach($sale->items as $index => $saleItem)
                                    <div class="item-row border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Product') }}</label>
                                                <select class="form-control" name="items[{{ $index }}][product_id]" onchange="updateDescription(this, {{ $index }})">
                                                    <option value="">{{ __('Select Product') }}</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                            data-name="{{ $product->name }}" 
                                                            data-price="{{ $product->price }}"
                                                            data-unit="{{ $product->unit }}"
                                                            {{ $saleItem->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} ({{ $product->barcode ?? 'N/A' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Description') }} *</label>
                                                <input type="text" class="form-control" name="items[{{ $index }}][description]" 
                                                       value="{{ $saleItem->product->name }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('Quantity') }} *</label>
                                                <input type="number" step="0.01" class="form-control" name="items[{{ $index }}][quantity]" 
                                                       value="{{ $saleItem->quantity }}" min="0.01" required onchange="calculateItemTotal({{ $index }})">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('Unit') }}</label>
                                                <input type="text" class="form-control" name="items[{{ $index }}][unit]" 
                                                       value="{{ $saleItem->product->unit ?? 'pcs' }}" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('Unit Price') }} *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" name="items[{{ $index }}][unit_price]" 
                                                           value="{{ $saleItem->unit_price }}" min="0" required onchange="calculateItemTotal({{ $index }})">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-10">
                                                <small class="text-muted">{{ __('Item Total:') }} $<span id="item-total-{{ $index }}">{{ number_format($saleItem->quantity * $saleItem->unit_price, 2) }}</span></small>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="item-row border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Product') }}</label>
                                            <select class="form-control" name="items[0][product_id]" onchange="updateDescription(this, 0)">
                                                <option value="">{{ __('Select Product') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                        data-name="{{ $product->name }}" 
                                                        data-price="{{ $product->price }}"
                                                        data-unit="{{ $product->unit }}">
                                                        {{ $product->name }} ({{ $product->sku ?? $product->barcode ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Description') }} *</label>
                                            <input type="text" class="form-control" name="items[0][description]" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Quantity') }} *</label>
                                            <input type="number" step="0.01" class="form-control" name="items[0][quantity]" 
                                                   value="1.00" min="0.01" required onchange="calculateItemTotal(0)">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Unit') }}</label>
                                            <input type="text" class="form-control" name="items[0][unit]" 
                                                   value="pcs" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Unit Price') }} *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control" name="items[0][unit_price]" 
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
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td>{{ __('Subtotal:') }}</td>
                                                <td class="text-end">$<span id="subtotal">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Discount:') }}</td>
                                                <td class="text-end">-$<span id="discount-amount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Tax:') }}</td>
                                                <td class="text-end">$<span id="tax-amount">0.00</span></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>{{ __('TOTAL:') }}</td>
                                                <td class="text-end">$<span id="total">0.00</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Create Invoice') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = {{ $sale && $sale->items->count() > 0 ? $sale->items->count() : 1 }};

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Product') }}</label>
                    <select class="form-control" name="items[${itemIndex}][product_id]" onchange="updateDescription(this, ${itemIndex})">
                        <option value="">{{ __('Select Product') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                data-name="{{ $product->name }}" 
                                data-price="{{ $product->price }}"
                                data-unit="{{ $product->unit }}">
                                {{ $product->name }} ({{ $product->barcode ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Description') }} *</label>
                    <input type="text" class="form-control" name="items[${itemIndex}][description]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Quantity') }} *</label>
                    <input type="number" step="0.01" class="form-control" name="items[${itemIndex}][quantity]" 
                           value="1.00" min="0.01" required onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Unit') }}</label>
                    <input type="text" class="form-control" name="items[${itemIndex}][unit]" 
                           value="pcs" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Unit Price') }} *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="items[${itemIndex}][unit_price]" 
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
    calculateTotals();
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    itemRow.remove();
    calculateTotals();
}

function updateDescription(select, index) {
    const option = select.options[select.selectedIndex];
    const descriptionInput = document.querySelector(`input[name="items[${index}][description]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    const unitInput = document.querySelector(`input[name="items[${index}][unit]"]`);
    
    if (option.value) {
        descriptionInput.value = option.dataset.name;
        priceInput.value = parseFloat(option.dataset.price).toFixed(2);
        unitInput.value = option.dataset.unit || 'pcs';
    } else {
        descriptionInput.value = '';
        priceInput.value = '0.00';
        unitInput.value = 'pcs';
    }
    
    calculateItemTotal(index);
}

function calculateItemTotal(index) {
    const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    const totalSpan = document.getElementById(`item-total-${index}`);
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const total = quantity * price;
    
    totalSpan.textContent = total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    // Calculate subtotal
    document.querySelectorAll('[id^="item-total-"]').forEach(span => {
        subtotal += parseFloat(span.textContent) || 0;
    });
    
    const discountRate = parseFloat(document.getElementById('discount_rate').value) || 0;
    const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
    
    const discountAmount = (subtotal * discountRate) / 100;
    const subtotalAfterDiscount = subtotal - discountAmount;
    const taxAmount = (subtotalAfterDiscount * taxRate) / 100;
    const total = subtotalAfterDiscount + taxAmount;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('discount-amount').textContent = discountAmount.toFixed(2);
    document.getElementById('tax-amount').textContent = taxAmount.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

// Event listeners
document.getElementById('tax_rate').addEventListener('input', calculateTotals);
document.getElementById('discount_rate').addEventListener('input', calculateTotals);

// Initial calculation
document.addEventListener('DOMContentLoaded', function() {
    // Calculate initial totals for pre-filled items
    document.querySelectorAll('.item-row').forEach((row, index) => {
        calculateItemTotal(index);
    });
});
</script>
@endsection