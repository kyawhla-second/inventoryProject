@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Generate Profit & Loss Statement') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profit-loss.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">{{ __('Period Start Date') }}</label>
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror" 
                                           id="period_start" name="period_start" value="{{ old('period_start') }}" required>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">{{ __('Period End Date') }}</label>
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror" 
                                           id="period_end" name="period_end" value="{{ old('period_end') }}" required>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="operating_expenses" class="form-label">{{ __('Operating Expenses ($)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('operating_expenses') is-invalid @enderror" 
                                   id="operating_expenses" name="operating_expenses" value="{{ old('operating_expenses', '0.00') }}" 
                                   placeholder="Enter additional operating expenses (rent, utilities, etc.)">
                            @error('operating_expenses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                {{ __('Additional operating expenses not covered by purchases and staff costs (e.g., rent, utilities, marketing, etc.)') }}
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> {{ __('What will be calculated:') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('Revenue: Total sales for the selected period') }}</li>
                                <li>{{ __('Cost of Goods Sold: Total purchases for the selected period') }}</li>
                                <li>{{ __('Staff Costs: Total approved staff charges for the selected period') }}</li>
                                <li>{{ __('Operating Expenses: Amount entered above') }}</li>
                                <li>{{ __('Net Profit: Revenue - (COGS + Staff Costs + Operating Expenses)') }}</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profit-loss.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Generate Statement') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    const startInput = document.getElementById('period_start');
    const endInput = document.getElementById('period_end');
    
    if (!startInput.value) {
        startInput.value = firstDay.toISOString().split('T')[0];
    }
    if (!endInput.value) {
        endInput.value = lastDay.toISOString().split('T')[0];
    }
});
</script>
@endsection