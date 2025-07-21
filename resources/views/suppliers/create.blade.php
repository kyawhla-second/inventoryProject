@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('Add New Supplier')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('suppliers.index') }}"> {{__('Back')}}</a>
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

<form action="{{ route('suppliers.store') }}" method="POST">
    @csrf

     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Name')}}</strong>
                <input type="text" name="name" class="form-control" placeholder="{{__('Name')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Contact Person')}}</strong>
                <input type="text" name="contact_person" class="form-control" placeholder="{{__('Contact Person')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Email')}}</strong>
                <input type="email" name="email" class="form-control" placeholder="{{__('Email')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Phone')}}</strong>
                <input type="text" name="phone" class="form-control" placeholder="{{__('Phone')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Address')}}</strong>
                <textarea class="form-control" style="height:150px" name="address" placeholder="{{__('Address')}}"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
        </div>
    </div>

</form>
@endsection
