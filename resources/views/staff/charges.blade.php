@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daily Charges - {{ $staff->full_name }}</h4>
                    <div>
                        <a href="{{ route('staff.charges.create', $staff) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Daily Charge
                        </a>
                        <a href="{{ route('staff.show', $staff) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-secondary">Filter</button>
                                <a href="{{ route('staff.charges', $staff) }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Charges Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hours Worked</th>
                                    <th>Overtime Hours</th>
                                    <th>Daily Rate</th>
                                    <th>Total Charge</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($charges as $charge)
                                    <tr>
                                        <td>{{ $charge->charge_date->format('M d, Y') }}</td>
                                        <td>{{ $charge->hours_worked }}</td>
                                        <td>{{ $charge->overtime_hours }}</td>
                                        <td>${{ number_format($charge->daily_rate, 2) }}</td>
                                        <td>${{ number_format($charge->total_charge, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $charge->status == 'paid' ? 'success' : ($charge->status == 'approved' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($charge->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $charge->notes ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('staff-charges.show', $charge) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('staff-charges.edit', $charge) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($charge->status == 'pending')
                                                    <form method="POST" action="{{ route('staff-charges.approve', $charge) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Approve this charge?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($charge->status == 'approved')
                                                    <form method="POST" action="{{ route('staff-charges.mark-paid', $charge) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-primary" 
                                                                onclick="return confirm('Mark as paid?')">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No charges found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($charges->count() > 0)
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>TOTALS</th>
                                        <th>{{ number_format($charges->sum('hours_worked'), 1) }}</th>
                                        <th>{{ number_format($charges->sum('overtime_hours'), 1) }}</th>
                                        <th>-</th>
                                        <th>${{ number_format($charges->sum('total_charge'), 2) }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{ $charges->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection