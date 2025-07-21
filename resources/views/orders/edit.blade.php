@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{__('Edit Order')}} #{{ $order->id }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label">{{__('Order Status')}}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{__('Pending')}}</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{__('Processing')}}</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>{{__('Shipped')}}</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>{{__('Completed')}}</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{__('Cancelled')}}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{__('Update Status')}}</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{__('Cancel')}}</a>    
            </form>
        </div>
    </div>
</div>
@endsection
