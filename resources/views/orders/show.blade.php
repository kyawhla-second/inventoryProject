@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{__('Order')}} #{{ $order->id }} {{__('Details')}}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{__('Back to Orders')}}</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">{{__('Customer Information')}}</div>
                <div class="card-body">
                    <p><strong>{{__('Name')}}:</strong> {{ optional($order->customer)->name ?? 'N/A' }}</p>
                    <p><strong>{{__('Email')}}:</strong> {{ optional($order->customer)->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">{{__('Order Summary')}}</div>
                <div class="card-body">
                    <p><strong>{{__('Order Date')}}:</strong> {{ $order->order_date }}</p>
                    <p><strong>{{__('Status')}}:</strong> <span class="badge bg-{{ $order->badge_class }}">{{ ucfirst($order->status) }}</span></p>
                    <p><strong>{{__('Total')}}:</strong> @money($order->total_amount)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{__('Order Items')}}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('Product')}}</th>
                            <th class="text-end">{{__('Quantity')}}</th>
                            <th class="text-end">{{__('Price')}}</th>
                            <th class="text-end">{{__('Subtotal')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($item->product)->name ?? 'Deleted Product' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">@money($item->price)</td>
                                <td class="text-end">@money($item->price * $item->quantity)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
