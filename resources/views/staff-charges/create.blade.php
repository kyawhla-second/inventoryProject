@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Add New Staff Daily Charge') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff-charges.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">{{ __('Staff Member') }}</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">{{ __('Select Staff Member') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                   id="charge_date" name="charge_date" value="{{ old('charge_date', date('Y-m-d')) }}" required>
                            @error('charge_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="daily_rate" class="form-label">{{ __('Daily Rate ($)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('daily_rate') is-invalid @enderror" 
                                   id="daily_rate" name="daily_rate" value="{{ old('daily_rate') }}" required>
                            @error('daily_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hours_worked" class="form-label">{{ __('Hours Worked') }}</label>
                                    <input type="number" step="0.5" class="form-control @error('hours_worked') is-invalid @enderror" 
                                           id="hours_worked" name="hours_worked" value="{{ old('hours_worked', '8.00') }}" required>
                                    @error('hours_worked')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="overtime_hours" class="form-label">{{ __('Overtime Hours') }}</label>
                                    <input type="number" step="0.5" class="form-control @error('overtime_hours') is-invalid @enderror" 
                                           id="overtime_hours" name="overtime_hours" value="{{ old('overtime_hours', '0.00') }}">
                                    @error('overtime_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="overtime_rate" class="form-label">{{ __('Overtime Rate ($/hour)') }}</label>
                            <input type="number" step="0.01" class="form-control @error('overtime_rate') is-invalid @enderror" 
                                   id="overtime_rate" name="overtime_rate" value="{{ old('overtime_rate') }}" 
                                   placeholder="Leave empty for 1.5x regular rate">
                            @error('overtime_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff-charges.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Save Charge') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyRateInput = document.getElementById('daily_rate');
    const hoursWorkedInput = document.getElementById('hours_worked');
    const overtimeHoursInput = document.getElementById('overtime_hours');
    const overtimeRateInput = document.getElementById('overtime_rate');

    function calculateTotal() {
        const dailyRate = parseFloat(dailyRateInput.value) || 0;
        const hoursWorked = parseFloat(hoursWorkedInput.value) || 0;
        const overtimeHours = parseFloat(overtimeHoursInput.value) || 0;
        const overtimeRate = parseFloat(overtimeRateInput.value) || (dailyRate * 1.5 / 8);

        const regularPay = dailyRate * (hoursWorked / 8);
        const overtimePay = overtimeHours * overtimeRate;
        const total = regularPay + overtimePay;

        // Display calculated total (you can add a display element if needed)
        console.log('Total calculated:', total.toFixed(2));
    }

    [dailyRateInput, hoursWorkedInput, overtimeHoursInput, overtimeRateInput].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
});
</script>
@endsection