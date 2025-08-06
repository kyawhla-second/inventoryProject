@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Staff Management</h4>
                    <div>
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <a href="{{ route('staff.create.simple') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Staff Member
                        </a>
                        
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by name, ID, or email" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="department" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                            {{ $department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="employment_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Staff Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Employment Type</th>
                                    <th>Status</th>
                                    <th>Hire Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    <tr>
                                        <td>
                                            @if($member->profile_photo)
                                                <img src="{{ asset('storage/' . $member->profile_photo) }}" 
                                                     alt="{{ $member->full_name }}" 
                                                     class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $member->employee_id }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $member->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $member->email }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $member->position }}</td>
                                        <td>{{ $member->department ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $member->employment_type_badge_class }}">
                                                {{ ucfirst(str_replace('_', ' ', $member->employment_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $member->status_badge_class }}">
                                                {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $member->hire_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('staff.show', $member) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('staff.charges', $member) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </a>
                                                <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No staff members found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection