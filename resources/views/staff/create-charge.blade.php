@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add Daily Charge - {{ $staff->full_name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.charges.store', $staff) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="charge_date">Date *</label>
                                    <input type="date" name="charge_date" id="charge_date" 
                                           class="form-control @error('charge_date') is-invalid @enderror" 
                                           value="{{ old('charge_date', date('Y-m-d')) }}" required>
                                    @error('charge_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hours_worked">Hours Worked *</label>
                                    <input type="number" name="hours_worked" id="hours_worked" step="0.5" min="0" max="24"
                                           class="form-control @error('hours_worked') is-invalid @enderror" 
                                           value="{{ old('hours_worked', 8) }}" required>
                                    @error('hours_worked')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="overtime_hours">Overtime Hours</label>
                                    <input type="number" name="overtime_hours" id="overtime_hours" step="0.5" min="0" max="12"
                                           class="form-control @error('overtime_hours') is-invalid @enderror" 
                                           value="{{ old('overtime_hours', 0) }}">
                                    @error('overtime_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Estimated Total Charge</label>
                                    <div class="form-control bg-light" id="estimated_charge">
                                        $0.00
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Any additional notes about this work day...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Daily Charge</button>
                            <a href="{{ route('staff.charges', $staff) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Staff Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $staff->full_name }}</p>
                    <p><strong>Position:</strong> {{ $staff->position }}</p>
                    <p><strong>Department:</strong> {{ $staff->department ?? 'N/A' }}</p>
                    <p><strong>Base Salary:</strong> ${{ number_format($staff->base_salary, 2) }}</p>
                    <p><strong>Hourly Rate:</strong> ${{ number_format($staff->hourly_rate, 2) }}</p>
                    <p><strong>Overtime Rate:</strong> ${{ number_format($staff->overtime_rate ?? ($staff->hourly_rate * 1.5), 2) }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Calculation Info</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>Regular Pay:</strong> Daily Rate × (Hours Worked ÷ 8)</p>
                        <p><strong>Overtime Pay:</strong> Overtime Hours × Overtime Rate</p>
                        <p><strong>Daily Rate:</strong> Base Salary ÷ 30 days</p>
                        <p><strong>Default Overtime Rate:</strong> Hourly Rate × 1.5</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hoursWorked = document.getElementById('hours_worked');
    const overtimeHours = document.getElementById('overtime_hours');
    const estimatedCharge = document.getElementById('estimated_charge');
    
    const baseSalary = {{ $staff->base_salary }};
    const hourlyRate = {{ $staff->hourly_rate }};
    const overtimeRate = {{ $staff->overtime_rate ?? ($staff->hourly_rate * 1.5) }};
    const dailyRate = baseSalary / 30;
    
    function calculateEstimate() {
        const hours = parseFloat(hoursWorked.value) || 0;
        const overtime = parseFloat(overtimeHours.value) || 0;
        
        const regularPay = dailyRate * (hours / 8);
        const overtimePay = overtime * overtimeRate;
        const total = regularPay + overtimePay;
        
        estimatedCharge.textContent = '$' + total.toFixed(2);
    }
    
    hoursWorked.addEventListener('input', calculateEstimate);
    overtimeHours.addEventListener('input', calculateEstimate);
    
    // Initial calculation
    calculateEstimate();
});
</script>
@endsection