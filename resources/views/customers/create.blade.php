@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{__('Add Customer')}}</h1>

    <form action="{{ route('customers.store') }}" method="POST">
        @include('customers._form')
    </form>
</div>
@endsection
