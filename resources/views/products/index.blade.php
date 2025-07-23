@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('Products')}}</h2>
            </div>
            <div class="pull-right mb-2">
                <a class="btn btn-success" href="{{ route('products.create') }}"> {{__('Create New Product')}}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="{{__('Search by name or description')}}..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">{{__('Search')}}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>{{__('No')}}</th>
            <th>{{__('Name')}}</th>
            <th>{{__('Category')}}</th>
            <th>{{__('Quantity')}}</th>
            <th>{{__('Unit')}}</th>
            <th>{{__('Price')}}</th>
            <th width="280px">{{__('Action')}}</th>
        </tr>
        @foreach ($products as $product)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ $product->quantity }}</td>
            <td>{{ $product->unit }}</td>
            <td>@money($product->price)</td>
            <td>
                <form action="{{ route('products.destroy',$product->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('products.show',$product->id) }}">{{__('Show')}}</a>
                    <a class="btn btn-primary" href="{{ route('products.edit',$product->id) }}">{{__('Edit')}}</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    {!! $products->links() !!}
@endsection
