@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('Show Sale Details')}}</h2>
            </div>
            <div class="pull-right">
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
