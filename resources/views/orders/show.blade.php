@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{__('Order')}} #{{ $order->id }} {{__('Details')}}</h1>
        <div>
            @if($order->invoice)
                <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-success me-2">
                    <i class="fas fa-file-invoice"></i> {{ __('View Invoice') }}
                </a>
                <a href="{{ route('invoices.pdf', $order->invoice) }}" class="btn btn-info me-2">
                    <i class="fas fa-download"></i> {{ __('Download Invoice') }}
                </a>
                <button type="button" class="btn btn-primary me-2" onclick="printInvoice({{ $order->invoice->id }})">
                    <i class="fas fa-print"></i> {{ __('Print Invoice') }}
                </button>
            @else
                <form method="POST" action="{{ route('invoices.create-from-order', $order) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-file-invoice"></i> {{ __('Create Invoice') }}
                    </button>
                </form>
            @endif
            
            @if($order->sales->count() == 0 && $order->status != 'cancelled')
                <form method="POST" action="{{ route('orders.convert-to-sale', $order) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning me-2" onclick="return confirm('{{ __('Convert this order to a sale? This will update inventory.') }}')">
                        <i class="fas fa-cash-register"></i> {{ __('Convert to Sale') }}
                    </button>
                </form>
            @endif
            
            <a href="{{ route('orders.create-purchase-form', $order) }}" class="btn btn-secondary me-2">
                <i class="fas fa-shopping-cart"></i> {{ __('Create Purchase Order') }}
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{__('Back to Orders')}}</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">{{__('Customer Information')}}</div>
                <div class="card-body">
                    <p><strong>{{__('Name')}}:</strong> {{ optional($order->customer)->name ?? 'N/A' }}</p>
                    <p><strong>{{__('Email')}}:</strong> {{ optional($order->customer)->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">{{__('Order Summary')}}</div>
                <div class="card-body">
                    <p><strong>{{__('Order Date')}}:</strong> {{ $order->order_date }}</p>
                    <p><strong>{{__('Status')}}:</strong> <span class="badge bg-{{ $order->badge_class }}">{{ ucfirst($order->status) }}</span></p>
                    <p><strong>{{__('Total')}}:</strong> @money($order->total_amount)</p>
                </div>
            </div>
        </div>
    </div>

    @if($order->invoice)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-file-invoice"></i> {{__('Invoice Information')}}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>{{__('Invoice Number')}}:</strong> {{ $order->invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{__('Invoice Date')}}:</strong> {{ $order->invoice->invoice_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{__('Due Date')}}:</strong> {{ $order->invoice->due_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>{{__('Status')}}:</strong> 
                                <span class="badge {{ $order->invoice->getStatusBadgeClass() }}">
                                    {{ ucfirst($order->invoice->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($order->invoice->isOverdue())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> 
                            {{__('This invoice is')}} {{ $order->invoice->getDaysOverdue() }} {{__('days overdue')}}!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Sales Section -->
    @if($order->sales->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-cash-register"></i> {{__('Related Sales')}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{__('Sale ID')}}</th>
                                    <th>{{__('Sale Date')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->sales as $sale)
                                <tr>
                                    <td>#{{ $sale->id }}</td>
                                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{__('View')}}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Purchases Section -->
    @if($order->purchases->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-shopping-cart"></i> {{__('Related Purchase Orders')}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{__('Purchase ID')}}</th>
                                    <th>{{__('Supplier')}}</th>
                                    <th>{{__('Purchase Date')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->purchases as $purchase)
                                <tr>
                                    <td>#{{ $purchase->id }}</td>
                                    <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $purchase->status == 'completed' ? 'success' : ($purchase->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{__('View')}}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">{{__('Order Items')}}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('Product')}}</th>
                            <th class="text-end">{{__('Quantity')}}</th>
                            <th class="text-end">{{__('Price')}}</th>
                            <th class="text-end">{{__('Subtotal')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($item->product)->name ?? 'Deleted Product' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">@money($item->price)</td>
                                <td class="text-end">@money($item->price * $item->quantity)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($order->invoice)
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
let currentInvoiceId = {{ $order->invoice->id ?? 'null' }};

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
@endif
@endsection
