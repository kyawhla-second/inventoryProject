@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $staff->full_name }}</h4>
                    <div>
                        <span class="badge {{ $staff->status_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $staff->status)) }}
                        </span>
                        <span class="badge {{ $staff->employment_type_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            @if($staff->profile_photo)
                                <img src="{{ asset('storage/' . $staff->profile_photo) }}" 
                                     alt="{{ $staff->full_name }}" 
                                     class="rounded-circle img-fluid" style="max-width: 150px;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Personal Information</h6>
                                    <p><strong>Employee ID:</strong> {{ $staff->employee_id }}</p>
                                    <p><strong>Email:</strong> {{ $staff->email }}</p>
                                    <p><strong>Phone:</strong> {{ $staff->phone ?? 'N/A' }}</p>
                                    <p><strong>Date of Birth:</strong> {{ $staff->date_of_birth?->format('M d, Y') ?? 'N/A' }}</p>
                                    @if($staff->address)
                                        <p><strong>Address:</strong> {{ $staff->address }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Employment Information</h6>
                                    <p><strong>Position:</strong> {{ $staff->position }}</p>
                                    <p><strong>Department:</strong> {{ $staff->department ?? 'N/A' }}</p>
                                    <p><strong>Hire Date:</strong> {{ $staff->hire_date->format('M d, Y') }}</p>
                                    <p><strong>Base Salary:</strong> ${{ number_format($staff->base_salary, 2) }}</p>
                                    <p><strong>Hourly Rate:</strong> ${{ number_format($staff->hourly_rate, 2) }}</p>
                                    @if($staff->supervisor)
                                        <p><strong>Supervisor:</strong> {{ $staff->supervisor->full_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($staff->emergency_contact_name || $staff->emergency_contact_phone)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Emergency Contact</h6>
                                <p><strong>Name:</strong> {{ $staff->emergency_contact_name ?? 'N/A' }}</p>
                                <p><strong>Phone:</strong> {{ $staff->emergency_contact_phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($staff->subordinates->count() > 0)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Direct Reports</h6>
                                <div class="row">
                                    @foreach($staff->subordinates as $subordinate)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <small>
                                                        <strong>{{ $subordinate->full_name }}</strong><br>
                                                        {{ $subordinate->position }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($staff->notes)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($staff->notes)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6>Recent Daily Charges</h6>
                            @if($recentCharges->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Hours</th>
                                                <th>Overtime</th>
                                                <th>Total Charge</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentCharges as $charge)
                                                <tr>
                                                    <td>{{ $charge->charge_date->format('M d, Y') }}</td>
                                                    <td>{{ $charge->hours_worked }}</td>
                                                    <td>{{ $charge->overtime_hours }}</td>
                                                    <td>${{ number_format($charge->total_charge, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $charge->status == 'paid' ? 'success' : ($charge->status == 'approved' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($charge->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent charges found</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Staff
                        </a>
                        <a href="{{ route('staff.charges', $staff) }}" class="btn btn-success">
                            <i class="fas fa-dollar-sign"></i> View All Charges
                        </a>
                        <a href="{{ route('staff.charges.create', $staff) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Daily Charge
                        </a>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>This Month Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-right">
                                <h4 class="text-primary">${{ number_format($stats['total_charges_this_month'], 2) }}</h4>
                                <small class="text-muted">Total Charges</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success">{{ number_format($stats['worked_hours_this_month'], 1) }}</h4>
                            <small class="text-muted">Hours Worked</small>
                        </div>
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-warning">{{ number_format($stats['overtime_hours_this_month'], 1) }}</h4>
                                <small class="text-muted">Overtime Hours</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ $stats['pending_charges'] }}</h4>
                            <small class="text-muted">Pending Charges</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($staff->user)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>System Access</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>User Account:</strong> {{ $staff->user->name }}</p>
                        <p><strong>Email:</strong> {{ $staff->user->email }}</p>
                        <p><strong>Role:</strong> {{ ucfirst($staff->user->role) }}</p>
                        <p><strong>Last Login:</strong> {{ $staff->user->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection