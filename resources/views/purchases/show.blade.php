@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{__('Purchase Details')}}</h1>
        <div>
            @if($purchase->order)
                <a href="{{ route('orders.show', $purchase->order) }}" class="btn btn-info me-2">
                    <i class="fas fa-list-alt"></i> {{ __('View Related Order') }}
                </a>
            @endif
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">{{__('Back to List')}}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{__('Purchase')}} #{{ $purchase->id }}
        </div>
        <div class="card-body">
            <p><strong>{{__('Date')}}:</strong> {{ $purchase->purchase_date }}</p>
            <p><strong>{{__('Supplier')}}:</strong> {{ $purchase->supplier->name }}</p>
            <p><strong>{{__('Total Amount')}}:</strong> ${{ number_format($purchase->total_amount, 2) }}</p>
            @if($purchase->order)
                <p><strong>{{__('Related Customer Order')}}:</strong> 
                    <a href="{{ route('orders.show', $purchase->order) }}" class="btn btn-sm btn-outline-primary">
                        Order #{{ $purchase->order->id }} - {{ $purchase->order->customer->name ?? 'No Customer' }}
                        ({{ $purchase->order->order_date->format('M d, Y') }})
                    </a>
                </p>
            @endif

            <h5 class="mt-4">{{__('Items')}}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{__('Product')}}</th>
                        <th>{{__('Quantity')}}</th>
                        <th>{{__('Cost')}}</th>
                        <th>{{__('Subtotal')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->cost, 2) }}</td>
                            <td>${{ number_format($item->quantity * $item->cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
