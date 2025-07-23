@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Staff Daily Charges') }}</h2>
                <a href="{{ route('staff-charges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add New Charge') }}
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('staff-charges.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">{{ __('Staff Member') }}</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="">{{ __('All Staff') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">{{ __('Status') }}</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-secondary">{{ __('Filter') }}</button>
                                <a href="{{ route('staff-charges.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Staff Member') }}</th>
                                    <th>{{ __('Daily Rate') }}</th>
                                    <th>{{ __('Hours Worked') }}</th>
                                    <th>{{ __('Overtime Hours') }}</th>
                                    <th>{{ __('Total Charge') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($charges as $charge)
                                    <tr>
                                        <td>{{ $charge->charge_date->format('Y-m-d') }}</td>
                                        <td>{{ $charge->user->name }}</td>
                                        <td>${{ number_format($charge->daily_rate, 2) }}</td>
                                        <td>{{ $charge->hours_worked }}</td>
                                        <td>{{ $charge->overtime_hours }}</td>
                                        <td>${{ number_format($charge->total_charge, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $charge->status == 'paid' ? 'success' : ($charge->status == 'approved' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($charge->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('staff-charges.show', $charge) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('staff-charges.edit', $charge) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($charge->status == 'pending')
                                                    <form method="POST" action="{{ route('staff-charges.approve', $charge) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this charge?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($charge->status == 'approved')
                                                    <form method="POST" action="{{ route('staff-charges.mark-paid', $charge) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Mark as paid?')">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('staff-charges.destroy', $charge) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No staff charges found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $charges->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection