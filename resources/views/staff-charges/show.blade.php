@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Staff Daily Charge Details') }}</h2>
                <div>
                    <a href="{{ route('staff-charges.edit', $staffCharge) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('staff-charges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('Basic Information') }}</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('Staff Member:') }}</strong></td>
                                    <td>{{ $staffCharge->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Role:') }}</strong></td>
                                    <td>{{ ucfirst($staffCharge->user->role) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Date:') }}</strong></td>
                                    <td>{{ $staffCharge->charge_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Status:') }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $staffCharge->status == 'paid' ? 'success' : ($staffCharge->status == 'approved' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($staffCharge->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('Work Details') }}</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('Daily Rate:') }}</strong></td>
                                    <td>${{ number_format($staffCharge->daily_rate, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Hours Worked:') }}</strong></td>
                                    <td>{{ $staffCharge->hours_worked }} hours</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Overtime Hours:') }}</strong></td>
                                    <td>{{ $staffCharge->overtime_hours }} hours</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Overtime Rate:') }}</strong></td>
                                    <td>${{ number_format($staffCharge->overtime_rate ?? 0, 2) }}/hour</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>{{ __('Calculation Breakdown') }}</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @php
                                        $regularPay = $staffCharge->daily_rate * ($staffCharge->hours_worked / 8);
                                        $overtimePay = $staffCharge->overtime_hours * ($staffCharge->overtime_rate ?? 0);
                                    @endphp
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h5>${{ number_format($regularPay, 2) }}</h5>
                                            <small>{{ __('Regular Pay') }}</small>
                                            <br>
                                            <small class="text-muted">({{ $staffCharge->hours_worked }}h × ${{ number_format($staffCharge->daily_rate / 8, 2) }}/h)</small>
                                        </div>
                                        <div class="col-md-1">
                                            <h5>+</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>${{ number_format($overtimePay, 2) }}</h5>
                                            <small>{{ __('Overtime Pay') }}</small>
                                            <br>
                                            <small class="text-muted">({{ $staffCharge->overtime_hours }}h × ${{ number_format($staffCharge->overtime_rate ?? 0, 2) }}/h)</small>
                                        </div>
                                        <div class="col-md-1">
                                            <h5>=</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-success">${{ number_format($staffCharge->total_charge, 2) }}</h4>
                                            <small><strong>{{ __('Total Charge') }}</strong></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($staffCharge->notes)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>{{ __('Notes') }}</h6>
                                <div class="alert alert-info">
                                    {{ $staffCharge->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted">
                                {{ __('Created:') }} {{ $staffCharge->created_at->format('M d, Y H:i') }}
                                @if($staffCharge->updated_at != $staffCharge->created_at)
                                    | {{ __('Updated:') }} {{ $staffCharge->updated_at->format('M d, Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            @if($staffCharge->status == 'pending')
                                <form method="POST" action="{{ route('staff-charges.approve', $staffCharge) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success me-2" onclick="return confirm('Approve this charge?')">
                                        <i class="fas fa-check"></i> {{ __('Approve') }}
                                    </button>
                                </form>
                            @endif
                            @if($staffCharge->status == 'approved')
                                <form method="POST" action="{{ route('staff-charges.mark-paid', $staffCharge) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary me-2" onclick="return confirm('Mark as paid?')">
                                        <i class="fas fa-dollar-sign"></i> {{ __('Mark as Paid') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection