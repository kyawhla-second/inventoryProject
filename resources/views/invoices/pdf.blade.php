<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .billing-section {
            margin: 30px 0;
        }
        
        .billing-info {
            float: left;
            width: 48%;
        }
        
        .billing-info h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .totals-table .total-row {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .notes-section {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #bdc3c7;
        }
        
        .notes-section h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background-color: #95a5a6; color: white; }
        .status-sent { background-color: #3498db; color: white; }
        .status-paid { background-color: #27ae60; color: white; }
        .status-overdue { background-color: #e74c3c; color: white; }
        .status-cancelled { background-color: #34495e; color: white; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-name">{{ config('app.name', 'Inventory Management') }}</div>
            <div>123 Business Street</div>
            <div>City, State 12345</div>
            <div>Phone: (555) 123-4567</div>
            <div>Email: info@company.com</div>
        </div>
        <div class="invoice-info">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div>Date: {{ $invoice->invoice_date->format('M d, Y') }}</div>
            <div>Due Date: {{ $invoice->due_date->format('M d, Y') }}</div>
            <div style="margin-top: 10px;">
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    <div class="billing-section clearfix">
        <div class="billing-info">
            <h4>Bill To:</h4>
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
        <div class="billing-info" style="float: right;">
            <h4>Payment Terms:</h4>
            {{ $invoice->payment_terms ?? 'Net 30' }}<br>
            @if($invoice->isOverdue())
                <div style="color: #e74c3c; font-weight: bold; margin-top: 10px;">
                    OVERDUE: {{ $invoice->getDaysOverdue() }} days
                </div>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 15%;" class="text-center">Quantity</th>
                <th style="width: 15%;" class="text-right">Unit Price</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if($item->product)
                            <br><small>SKU: {{ $item->product->barcode ?? 'N/A' }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>Discount ({{ $invoice->discount_rate }}%):</td>
                    <td class="text-right">-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td>Tax ({{ $invoice->tax_rate }}%):</td>
                    <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
        <div class="notes-section">
            <h4>Notes:</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('M d, Y H:i') }} | 
           @if($invoice->print_count > 0)
               Print #{{ $invoice->print_count }}
           @else
               Original
           @endif
        </p>
    </div>
</body>
</html>