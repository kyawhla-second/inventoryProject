@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('Edit Product') }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('products.index') }}"> {{ __('Back') }}</a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Name') }}:</strong>
                    <input type="text" name="name" value="{{ $product->name }}" class="form-control"
                        placeholder="Name">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Description') }}:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ $product->description }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Barcode') }}:</strong>
                    <input type="text" name="barcode" value="{{ $product->barcode }}" class="form-control"
                        placeholder="Barcode">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Image') }}:</strong>
                    <input type="file" name="image" class="form-control">
                    <img src="/images/{{ $product->image }}" width="100px" class="mt-2">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Category') }}:</strong>
                    <select name="category_id" class="form-control">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Quantity') }}:</strong>
                    <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control"
                        placeholder="Quantity">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Unit') }}:</strong>
                    <select name="unit" class="form-control">
                        <option value="oz" {{ $product->unit == 'viss' ? 'selected' : '' }}>{{ __('viss(viss)') }}
                        </option>
                        <option value="box" {{ $product->unit == 'box' ? 'selected' : '' }}>{{ __('Boxes (box)') }}
                        </option>
                        <option value="pack" {{ $product->unit == 'pack' ? 'selected' : '' }}>{{ __('Packs (pack)') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Price') }}:</strong>
                    <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control"
                        placeholder="Price">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Cost') }}:</strong>
                    <input type="number" step="0.01" name="cost" value="{{ $product->cost }}" class="form-control"
                        placeholder="Cost">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Minimum Stock Level') }}:</strong>
                    <input type="number" name="minimum_stock_level" value="{{ $product->minimum_stock_level }}"
                        class="form-control" placeholder="Minimum Stock Level">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
        </div>

    </form>
@endsection
