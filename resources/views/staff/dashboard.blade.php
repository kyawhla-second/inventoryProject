@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Staff Dashboard</h4>
                    <a href="{{ route('staff.index') }}" class="btn btn-primary">
                        <i class="fas fa-users"></i> View All Staff
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $stats['total_staff'] }}</h3>
                                            <p class="mb-0">Total Staff</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $stats['active_staff'] }}</h3>
                                            <p class="mb-0">Active Staff</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $stats['on_leave'] }}</h3>
                                            <p class="mb-0">On Leave</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $stats['pending_charges'] }}</h3>
                                            <p class="mb-0">Pending Charges</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Hires -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Hires (Last 30 Days)</h5>
                                </div>
                                <div class="card-body">
                                    @if($recentHires->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentHires as $hire)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $hire->full_name }}</strong><br>
                                                        <small class="text-muted">{{ $hire->position }} - {{ $hire->department }}</small>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ $hire->hire_date->format('M d, Y') }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No recent hires in the last 30 days.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Department Statistics -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Staff by Department</h5>
                                </div>
                                <div class="card-body">
                                    @if($departmentStats->count() > 0)
                                        @foreach($departmentStats as $dept)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ $dept->department }}</span>
                                                <div>
                                                    <span class="badge bg-primary">{{ $dept->count }}</span>
                                                </div>
                                            </div>
                                            <div class="progress mb-3" style="height: 10px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ ($dept->count / $stats['total_staff']) * 100 }}%">
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No department data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('staff.create') }}" class="btn btn-primary btn-block">
                                                <i class="fas fa-user-plus"></i> Add New Staff
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('staff-charges.index') }}" class="btn btn-success btn-block">
                                                <i class="fas fa-dollar-sign"></i> View All Charges
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('staff-charges.create') }}" class="btn btn-info btn-block">
                                                <i class="fas fa-plus"></i> Add Daily Charge
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('staff.index', ['status' => 'active']) }}" class="btn btn-outline-primary btn-block">
                                                <i class="fas fa-filter"></i> Active Staff Only
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection