@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Profit & Loss Statement') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profit-loss.update', $profitLoss) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">{{ __('Period Start Date') }}</label>
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror" 
                                           id="period_start" name="period_start" value="{{ old('period_start', $profitLoss->period_start->format('Y-m-d')) }}" required>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">{{ __('Period End Date') }}</label>
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror" 
                                           id="period_end" name="period_end" value="{{ old('period_end', $profitLoss->period_end->format('Y-m-d')) }}" required>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="operating_expenses" class="form-label">{{ __('Operating Expenses ($)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('operating_expenses') is-invalid @enderror" 
                                   id="operating_expenses" name="operating_expenses" value="{{ old('operating_expenses', $profitLoss->operating_expenses) }}" 
                                   placeholder="Enter additional operating expenses (rent, utilities, etc.)">
                            @error('operating_expenses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                {{ __('Additional operating expenses not covered by purchases and staff costs (e.g., rent, utilities, marketing, etc.)') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $profitLoss->status) == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="finalized" {{ old('status', $profitLoss->status) == 'finalized' ? 'selected' : '' }}>{{ __('Finalized') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                {{ __('Note: Finalized statements cannot be edited further.') }}
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> {{ __('Important:') }}</h6>
                            <p class="mb-0">{{ __('Updating the period dates will recalculate all financial data based on sales, purchases, and staff charges within the new date range.') }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profit-loss.show', $profitLoss) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Update Statement') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection