@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Staff Daily Charge') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff-charges.update', $staffCharge) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">{{ __('Staff Member') }}</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">{{ __('Select Staff Member') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $staffCharge->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="charge_date" class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control @error('charge_date') is-invalid @enderror" 
                                   id="charge_date" name="charge_date" value="{{ old('charge_date', $staffCharge->charge_date->format('Y-m-d')) }}" required>
                            @error('charge_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="daily_rate" class="form-label">{{ __('Daily Rate ($)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('daily_rate') is-invalid @enderror" 
                                   id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $staffCharge->daily_rate) }}" required>
                            @error('daily_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hours_worked" class="form-label">{{ __('Hours Worked') }}</label>
                                    <input type="number" step="0.5" class="form-control @error('hours_worked') is-invalid @enderror" 
                                           id="hours_worked" name="hours_worked" value="{{ old('hours_worked', $staffCharge->hours_worked) }}" required>
                                    @error('hours_worked')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="overtime_hours" class="form-label">{{ __('Overtime Hours') }}</label>
                                    <input type="number" step="0.5" class="form-control @error('overtime_hours') is-invalid @enderror" 
                                           id="overtime_hours" name="overtime_hours" value="{{ old('overtime_hours', $staffCharge->overtime_hours) }}">
                                    @error('overtime_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="overtime_rate" class="form-label">{{ __('Overtime Rate ($/hour)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('overtime_rate') is-invalid @enderror" 
                                   id="overtime_rate" name="overtime_rate" value="{{ old('overtime_rate', $staffCharge->overtime_rate) }}" 
                                   placeholder="Leave empty for 1.5x regular rate">
                            @error('overtime_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $staffCharge->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="approved" {{ old('status', $staffCharge->status) == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                <option value="paid" {{ old('status', $staffCharge->status) == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $staffCharge->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff-charges.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Update Charge') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection