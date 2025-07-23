@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Profit & Loss Statements') }}</h2>
                <div>
                    <a href="{{ route('profit-loss.quick') }}" class="btn btn-info me-2">
                        <i class="fas fa-chart-line"></i> {{ __('Quick Report') }}
                    </a>
                    <a href="{{ route('profit-loss.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Generate New Statement') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Period') }}</th>
                                    <th>{{ __('Total Revenue') }}</th>
                                    <th>{{ __('Total Expenses') }}</th>
                                    <th>{{ __('Net Profit') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statements as $statement)
                                    <tr>
                                        <td>
                                            {{ $statement->period_start->format('M d, Y') }} - 
                                            {{ $statement->period_end->format('M d, Y') }}
                                        </td>
                                        <td class="text-success">${{ number_format($statement->total_revenue, 2) }}</td>
                                        <td class="text-danger">${{ number_format($statement->total_expenses, 2) }}</td>
                                        <td class="{{ $statement->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                            ${{ number_format($statement->net_profit, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $statement->status == 'finalized' ? 'success' : 'warning' }}">
                                                {{ ucfirst($statement->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $statement->creator->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('profit-loss.show', $statement) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($statement->status == 'draft')
                                                    <a href="{{ route('profit-loss.edit', $statement) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('profit-loss.finalize', $statement) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Finalize this statement? This cannot be undone.')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('profit-loss.destroy', $statement) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No profit & loss statements found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $statements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection