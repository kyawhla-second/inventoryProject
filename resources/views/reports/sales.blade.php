@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{__('Sales Report')}}</h1>
        <button class="btn btn-primary" onclick="window.print()">{{__('Print')}}</button>
    </div>

    <div class="card">
        <div class="card-header">
            {{__('Sales from')}} <strong>{{ $startDate }}</strong> {{__('to')}} <strong>{{ $endDate }}</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{__('Sale ID')}}</th>
                        <th>{{__('Date')}}</th>
                        <th>{{__('Products')}}</th>
                        <th>{{__('Total Amount')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>#{{ $sale->id }}</td>
                            <td>{{ $sale->sale_date }}</td>
                            <td>
                                <ul>
                                    @foreach ($sale->items as $item)
                                        <li>{{ $item->product->name }} ({{ $item->quantity }} x @money($item->unit_price))</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>@money($sale->total_amount)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{__('No sales found for this period')}}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">{{__('Total Sales')}}:</th>
                        <th>@money($totalSales)</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
