<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }} - Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .invoice-container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .company-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #e74c3c;
            text-align: right;
        }
        
        .invoice-number {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background-color: #6c757d; color: white; }
        .status-sent { background-color: #17a2b8; color: white; }
        .status-paid { background-color: #28a745; color: white; }
        .status-overdue { background-color: #dc3545; color: white; }
        .status-cancelled { background-color: #343a40; color: white; }
        
        .billing-section {
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            border-top: 2px solid #007bff;
        }
        
        .totals-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
        }
        
        .total-row {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: none;
            }
            
            .print-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons">
        <button class="btn btn-primary me-2" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <div class="invoice-container">
        <div class="company-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="company-name">{{ config('app.name', 'Inventory Management') }}</div>
                    <div class="mt-2">
                        <div>123 Business Street</div>
                        <div>City, State 12345</div>
                        <div>Phone: (555) 123-4567</div>
                        <div>Email: info@company.com</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                    <div class="mt-2">
                        <div>Date: {{ $invoice->invoice_date->format('M d, Y') }}</div>
                        <div>Due Date: {{ $invoice->due_date->format('M d, Y') }}</div>
                        <div class="mt-2">
                            <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="billing-section">
            <div class="row">
                <div class="col-md-6">
                    <h5>Bill To:</h5>
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
                        <strong>Walk-in Customer</strong>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>Payment Terms:</h5>
                    {{ $invoice->payment_terms ?? 'Net 30' }}<br>
                    @if($invoice->isOverdue())
                        <div class="text-danger fw-bold mt-2">
                            OVERDUE: {{ $invoice->getDaysOverdue() }} days
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th style="width: 15%;" class="text-center">Quantity</th>
                        <th style="width: 15%;" class="text-end">Unit Price</th>
                        <th style="width: 20%;" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->description }}</strong>
                                @if($item->product)
                                    <br><small class="text-muted">SKU: {{ $item->product->barcode ?? 'N/A' }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
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
                    <h5>Notes:</h5>
                    <div class="alert alert-info">
                        {{ $invoice->notes }}
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="totals-section">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td>Discount ({{ $invoice->discount_rate }}%):</td>
                                <td class="text-end">-${{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if($invoice->tax_amount > 0)
                            <tr>
                                <td>Tax ({{ $invoice->tax_rate }}%):</td>
                                <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td><strong>TOTAL:</strong></td>
                            <td class="text-end"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 border-top">
            <p class="mb-1"><strong>Thank you for your business!</strong></p>
            <small class="text-muted">
                Generated on {{ now()->format('M d, Y H:i') }} | 
                @if($invoice->print_count > 0)
                    Print #{{ $invoice->print_count + 1 }}
                @else
                    Original
                @endif
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>