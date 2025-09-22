@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('Invoice') }} {{ $invoice->invoice_number }}</h2>
                <div>
                    <a href="{{ route('invoices.preview', $invoice) }}" class="btn btn-secondary me-2" target="_blank">
                        <i class="fas fa-search"></i> {{ __('Preview') }}
                    </a>
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-success me-2">
                        <i class="fas fa-download"></i> {{ __('Download PDF') }}
                    </a>
                    <button type="button" class="btn btn-primary me-2" onclick="printInvoice({{ $invoice->id }})">
                        <i class="fas fa-print"></i> {{ __('Print') }}
                    </button>
                    @if($invoice->status !== 'paid')
                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                    @endif
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Invoice Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>{{ __('Bill To:') }}</h6>
                                    @if($invoice->customer)
                                        <strong>{{ $invoice->customer->name }}</strong><br>
                                        @if($invoice->customer->email)
                                            {{ $invoice->customer->email }}<br>
                                        @endif
                                        @if($invoice->customer->phone)
                                            {{ $invoice->customer->phone }}<br>
                                        @endif
                                        @if($invoice->customer->address)
                                            {{ $invoice->customer->address }}
                                        @endif
                                    @else
                                        <strong>{{ __('Walk-in Customer') }}</strong>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>{{ __('Invoice Information:') }}</h6>
                                    <strong>{{ __('Invoice #:') }}</strong> {{ $invoice->invoice_number }}<br>
                                    <strong>{{ __('Date:') }}</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                                    <strong>{{ __('Due Date:') }}</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                                    <strong>{{ __('Payment Terms:') }}</strong> {{ $invoice->payment_terms }}<br>
                                    <strong>{{ __('Status:') }}</strong> 
                                    <span class="badge {{ $invoice->getStatusBadgeClass() }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                    @if($invoice->isOverdue())
                                        <br><span class="text-danger"><strong>{{ $invoice->getDaysOverdue() }} {{ __('days overdue') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Description') }}</th>
                                            <th class="text-center">{{ __('Quantity') }}</th>
                                            <th class="text-center">{{ __('Unit') }}</th>
                                            <th class="text-end">{{ __('Unit Price') }}</th>
                                            <th class="text-end">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->description }}</strong>
                                                    @if($item->product)
                                                        <br><small class="text-muted">{{ __('SKU:') }} {{ $item->product->barcode ?? 'N/A' }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="text-center">{{ $item->unit ?? 'pcs' }}</td>
                                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    @if($invoice->notes)
                                        <h6>{{ __('Notes:') }}</h6>
                                        <div class="alert alert-info">
                                            {{ $invoice->notes }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>{{ __('Subtotal:') }}</strong></td>
                                            <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                                        </tr>
                                        @if($invoice->discount_amount > 0)
                                            <tr>
                                                <td>{{ __('Discount') }} ({{ $invoice->discount_rate }}%):</td>
                                                <td class="text-end">-${{ number_format($invoice->discount_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($invoice->tax_amount > 0)
                                            <tr>
                                                <td>{{ __('Tax') }} ({{ $invoice->tax_rate }}%):</td>
                                                <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td><strong>{{ __('TOTAL:') }}</strong></td>
                                            <td class="text-end"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('Quick Actions') }}</h6>
                        </div>
                        <div class="card-body">
                            @if($invoice->status === 'draft')
                                <form method="POST" action="{{ route('invoices.mark-sent', $invoice) }}" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info w-100" onclick="return confirm('{{ __('Mark as sent?') }}')">
                                        <i class="fas fa-paper-plane"></i> {{ __('Mark as Sent') }}
                                    </button>
                                </form>
                            @endif
                            @if($invoice->status !== 'paid')
                                <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Mark as paid?') }}')">
                                        <i class="fas fa-check"></i> {{ __('Mark as Paid') }}
                                    </button>
                                </form>
                            @endif
                            @if($invoice->status !== 'paid')
                                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('{{ __('Are you sure?') }}')">
                                        <i class="fas fa-trash"></i> {{ __('Delete Invoice') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('Print Information') }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Print Count:') }}</strong> {{ $invoice->print_count }}</p>
                            @if($invoice->printed_at)
                                <p><strong>{{ __('Last Printed:') }}</strong> {{ $invoice->printed_at->format('M d, Y H:i') }}</p>
                            @else
                                <p class="text-muted">{{ __('Never printed') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6>{{ __('Invoice History') }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Created:') }}</strong> {{ $invoice->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>{{ __('Created By:') }}</strong> {{ $invoice->creator->name }}</p>
                            @if($invoice->updated_at != $invoice->created_at)
                                <p><strong>{{ __('Last Updated:') }}</strong> {{ $invoice->updated_at->format('M d, Y H:i') }}</p>
                            @endif
                            @if($invoice->sale)
                                <p><strong>{{ __('Related Sale:') }}</strong> 
                                    <a href="{{ route('sales.show', $invoice->sale) }}">#{{ $invoice->sale->id }}</a>
                                </p>
                            @endif
                            @if($invoice->order)
                                <p><strong>{{ __('Related Order:') }}</strong> 
                                    <a href="{{ route('orders.show', $invoice->order) }}">#{{ $invoice->order->id }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
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
let currentInvoiceId = {{ $invoice->id }};

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