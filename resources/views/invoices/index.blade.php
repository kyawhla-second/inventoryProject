@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Invoices') }}</h2>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Create Invoice') }}
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('invoices.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">{{ __('Status') }}</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('Sent') }}</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_id" class="form-label">{{ __('Customer') }}</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">{{ __('All Customers') }}</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-secondary">{{ __('Filter') }}</button>
                                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_overdue" name="show_overdue" value="1" {{ request('show_overdue') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_overdue">
                                        {{ __('Show only overdue invoices') }}
                                    </label>
                                </div>
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
                                    <th>{{ __('Invoice #') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Print Count') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr class="{{ $invoice->isOverdue() ? 'table-danger' : '' }}">
                                        <td>
                                            <strong>{{ $invoice->invoice_number }}</strong>
                                            @if($invoice->isOverdue())
                                                <br><small class="text-danger">{{ $invoice->getDaysOverdue() }} days overdue</small>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->customer->name ?? 'Walk-in Customer' }}</td>
                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $invoice->getStatusBadgeClass() }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $invoice->print_count }}
                                            @if($invoice->printed_at)
                                                <br><small class="text-muted">{{ $invoice->printed_at->format('M d, H:i') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('invoices.preview', $invoice) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="printInvoice({{ $invoice->id }})">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                @if($invoice->status !== 'paid')
                                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($invoice->status === 'draft')
                                                    <form method="POST" action="{{ route('invoices.mark-sent', $invoice) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Mark as sent?')">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($invoice->status !== 'paid')
                                                    <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark as paid?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($invoice->status !== 'paid')
                                                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" style="display: inline;">
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
                                        <td colspan="8" class="text-center">{{ __('No invoices found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Print Invoice') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="printForm">
                    <div class="mb-3">
                        <label for="printer_select" class="form-label">{{ __('Select Printer') }}</label>
                        <select class="form-control" id="printer_select" name="printer_name">
                            <option value="default">{{ __('Default Printer') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="open_preview" checked>
                            <label class="form-check-label" for="open_preview">
                                {{ __('Open preview before printing') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="executePrint()">{{ __('Print') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentInvoiceId = null;

function printInvoice(invoiceId) {
    currentInvoiceId = invoiceId;
    
    // Load available printers
    fetch('{{ route("api.printers") }}')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('printer_select');
            select.innerHTML = '<option value="default">Default Printer</option>';
            
            data.printers.forEach(printer => {
                if (printer !== 'Default Printer') {
                    select.innerHTML += `<option value="${printer}">${printer}</option>`;
                }
            });
        })
        .catch(error => {
            console.error('Error loading printers:', error);
        });
    
    // Show modal
    new bootstrap.Modal(document.getElementById('printModal')).show();
}

function executePrint() {
    if (!currentInvoiceId) return;
    
    const printerName = document.getElementById('printer_select').value;
    const openPreview = document.getElementById('open_preview').checked;
    
    if (openPreview) {
        // Open preview first
        window.open(`/invoices/${currentInvoiceId}/preview`, '_blank');
    }
    
    // Send to printer
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/invoices/${currentInvoiceId}/send-to-printer`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const printerInput = document.createElement('input');
    printerInput.type = 'hidden';
    printerInput.name = 'printer_name';
    printerInput.value = printerName;
    form.appendChild(printerInput);
    
    document.body.appendChild(form);
    form.submit();
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('printModal')).hide();
}
</script>
@endsection