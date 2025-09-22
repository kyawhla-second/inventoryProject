@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{__('Customer Orders')}}</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary"> {{__('Create New Order')}}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Form -->
    <form action="{{ route('orders.index') }}" method="GET" class="input-group mb-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by ID, customer, status">
        <button class="btn btn-outline-secondary" type="submit">{{__('Search')}}</button>
        @if(request('q'))
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">{{__('Clear')}}</a>
        @endif
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{__('Order ID')}}</th>
                        <th>{{__('Customer')}}</th>
                        <th>{{__('Order Date')}}</th>
                        <th>{{__('Total Amount')}}</th>
                        <th>{{__('Status')}}</th>
                        <th>{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ optional($order->customer)->name ?? 'N/A' }}</td>
                            <td>{{ $order->order_date }}</td>
                            <td>@money($order->total_amount)</td>
                            <td>
                                <span class="badge bg-{{ $order->badge_class }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">{{__('View')}}</a>
                                @if($order->status !== 'completed')
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-primary">{{__('Edit')}}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{__('No orders found.')}}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
