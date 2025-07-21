@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{__('Sales Report')}}</h1>

    <div class="card">
        <div class="card-header">{{__('Generate Sales Report')}}</div>
        <div class="card-body">
            <form action="{{ route('reports.sales') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="start_date">{{__('Start Date')}}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="end_date">{{__('End Date')}}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4">{{__('Generate')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
