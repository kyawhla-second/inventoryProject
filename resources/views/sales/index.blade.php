@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('Sales')}}</h2>
            </div>
            <div class="pull-right mb-2">
                <a class="btn btn-success" href="{{ route('sales.create') }}"> {{__('Record New Sale')}}</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>{{__('No')}}</th>
            <th>{{__('Sale Date')}}</th>
            <th>{{__('Total Amount')}}</th>
            <th width="280px">{{__('Action')}}</th>
        </tr>
        @foreach ($sales as $sale)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $sale->sale_date }}</td>
            <td>@money($sale->total_amount)</td>
            <td>
                <form action="{{ route('sales.destroy',$sale->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('sales.show',$sale->id) }}">{{__('Show')}}</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    {!! $sales->links() !!}
@endsection
