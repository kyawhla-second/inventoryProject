@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('Show Sale Details')}}</h2>
            </div>
            <div class="pull-right">
                @if(!$sale->invoice)
                    <form method="POST" action="{{ route('invoices.create-from-sale', $sale) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-file-invoice"></i> {{ __('Create Invoice') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('invoices.show', $sale->invoice) }}" class="btn btn-info me-2">
                        <i class="fas fa-file-invoice"></i> {{ __('View Invoice') }}
                    </a>
                @endif
                
                @if($sale->order)
                    <a href="{{ route('orders.show', $sale->order) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-list-alt"></i> {{ __('View Related Order') }}
                    </a>
                @endif
                <a class="btn btn-primary" href="{{ route('sales.index') }}"> {{__('Back')}}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Sale ID')}}:</strong>
                {{ $sale->id }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Sale Date')}}:</strong>
                {{ $sale->sale_date }}
            </div>
        </div>
        @if($sale->order)
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{__('Related Order')}}:</strong>
                    <a href="{{ route('orders.show', $sale->order) }}" class="btn btn-sm btn-outline-primary">
                        Order #{{ $sale->order->id }} - {{ $sale->order->order_date->format('M d, Y') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <h4 class="mt-4">{{__('Sold Items')}}</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{__('Product')}}</th>
                <th>{{__('Quantity')}}</th>
                <th>{{__('Unit Price')}}</th>
                <th>{{__('Subtotal')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>@money($item->unit_price)</td>
                    <td>@money($item->quantity * $item->unit_price)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row mt-3">
        <div class="col-xs-12 col-sm-12 col-md-12 text-right">
            <h4>{{__('Total Amount')}}: @money($sale->total_amount)</h4>
        </div>
    </div>
@endsection
