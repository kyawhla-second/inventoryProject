@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{__('Create Order')}}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{__('Back to Orders')}}</a>
    </div>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="customer_id" class="form-label">{{__('Customer')}}</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="order_date" class="form-label">{{__('Order Date')}}</label>
                <input type="date" name="order_date" id="order_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
            </div>
        </div>

        <h4 class="mt-3">{{__('Order Items')}}</h4>
        <table class="table table-bordered" id="items_table">
            <thead>
                <tr>
                    <th>{{__('Product')}}</th>
                    <th>{{__('Quantity')}}</th>
                    <th>{{__('Price')}}</th>
                    <th>{{__('Subtotal')}}</th>
                    <th>{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-secondary" id="add_item">{{__('Add Item')}}</button>
        <button type="submit" class="btn btn-primary">{{__('Save Order')}}</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let index = 0;
        const addItemBtn = document.getElementById('add_item');
        const tbody = document.querySelector('#items_table tbody');

        addItemBtn.addEventListener('click', () => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="products[${index}][product_id]" class="form-select" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="products[${index}][quantity]" class="form-control qty" min="1" required></td>
                <td><input type="number" name="products[${index}][price]" class="form-control price" step="0.01" min="0" required></td>
                <td class="subtotal text-end">Ks 0.00</td>
                <td><button type="button" class="btn btn-danger btn-sm remove">{{__('Remove')}}</button></td>
            `;
            tbody.appendChild(row);
            index++;
        });

        tbody.addEventListener('input', function (e) {
            if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
                const tr = e.target.closest('tr');
                const qty = parseFloat(tr.querySelector('.qty').value) || 0;
                const price = parseFloat(tr.querySelector('.price').value) || 0;
                tr.querySelector('.subtotal').textContent = `Ks ${(qty * price).toFixed(2)}`;
            }
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection
