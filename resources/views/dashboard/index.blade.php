@extends("layouts.app")
@push('styles')
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #8d99ae;
            --gray-light: #edf2f4;
            --border: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
        }

        .container-fluid {
            padding: 20px;
            max-width: 1920px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .page-actions {
            display: flex;
            gap: 12px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 20px;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 32px;
            opacity: 0.8;
        }

        .stat-card .stat-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-card .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Status Cards */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .status-card {
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            color: white;
            transition: transform 0.2s;
        }

        .status-card:hover {
            transform: scale(1.03);
        }

        .status-card .status-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .status-card .status-value {
            font-size: 24px;
            font-weight: 700;
        }

        /* Financial Section */
        .financial-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
        }

        .month-selector-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .month-selector-label {
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
        }

        #monthSelector {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            color: var(--dark);
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        #monthSelector:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .financial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .financial-card {
            padding: 20px;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .financial-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(40px, -40px);
        }

        .financial-card .financial-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 32px;
            opacity: 0.8;
        }

        .financial-card .financial-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        .financial-card .financial-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .financial-card .financial-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .financial-summary {
            padding: 20px;
            border-radius: 12px;
            color: white;
            margin-bottom: 24px;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 600;
        }

        /* Content Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: var(--gray-light);
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
        }

        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Progress Bar */
        .progress-container {
            margin-bottom: 16px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .progress-bar {
            height: 10px;
            background-color: var(--gray-light);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        /* Charts Row */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-top: 24px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Utility Classes */
        .text-success { color: #10b981 !important; }
        .text-danger { color: #ef4444 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-info { color: #3b82f6 !important; }

        .bg-success { background-color: #10b981 !important; }
        .bg-danger { background-color: #ef4444 !important; }
        .bg-warning { background-color: #f59e0b !important; }
        .bg-info { background-color: #3b82f6 !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .bg-secondary { background-color: var(--secondary) !important; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .form-control {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .form-control-sm {
            padding: 6px 10px;
            font-size: 13px;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-3 {
            gap: 12px;
        }

        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-5 { margin-bottom: 20px; }

        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }

        .text-center { text-align: center; }
        .text-xs { font-size: 12px; }
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }

        .font-weight-bold { font-weight: 700; }
        .font-weight-semibold { font-weight: 600; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --dark: #f8f9fa;
                --light: #1a202c;
                --gray: #a0aec0;
                --gray-light: #2d3748;
                --border: #4a5568;
            }

            body {
                background-color: #1a202c;
                color: var(--dark);
            }

            .card {
                background: #2d3748;
            }

            .data-table th {
                background-color: #4a5568;
            }

            .data-table tr:hover {
                background-color: rgba(255, 255, 255, 0.05);
            }

            #monthSelector {
                background: #4a5568;
                border-color: #718096;
                color: white;
            }

            #monthSelector option {
                background: #4a5568;
                color: white;
            }
        }
        
        /* Money formatting */
        .money {
            font-weight: 600;
        }
        
        /* Status badge colors */
        .status-pending { background-color: #3b82f6; }
        .status-processing { background-color: #8b5cf6; }
        .status-shipped { background-color: #f59e0b; }
        .status-completed { background-color: #10b981; }
        .status-cancelled { background-color: #ef4444; }
    </style>
@endpush
    @section("content")
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h1>Business Dashboard</h1>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <!-- Total Products Card -->
            <div class="stat-card bg-primary">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-title">Total Products</div>
                <div class="stat-value">{{ $totalProducts ?? 245 }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> 12.5% from last month
                </div>
            </div>

            <!-- Total Customers Card -->
            <div class="stat-card bg-info">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">Total Customers</div>
                <div class="stat-value">{{ $totalCustomers ?? 128 }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> 5.2% from last month
                </div>
            </div>

            <!-- Total Suppliers Card -->
            <div class="stat-card bg-secondary">
                <div class="stat-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-title">Total Suppliers</div>
                <div class="stat-value">{{ $totalSuppliers ?? 18 }}</div>
                <div class="stat-change text-warning">
                    <i class="fas fa-minus"></i> No change
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="stat-card bg-success">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-title">Total Orders</div>
                <div class="stat-value">{{ $totalOrders ?? 542 }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> 8.7% from last month
                </div>
            </div>
        </div>

        <!-- Order Status Overview -->
        <div class="status-grid">
            @php
                $statusClasses = [
                    'pending' => 'status-pending',
                    'processing' => 'status-processing', 
                    'shipped' => 'status-shipped',
                    'completed' => 'status-completed',
                    'cancelled' => 'status-cancelled'
                ];
            @endphp
            
            @foreach (['pending', 'processing', 'shipped', 'completed', 'cancelled'] as $status)
                <div class="status-card {{ $statusClasses[$status] }}">
                    <div class="status-title">{{ ucfirst($status) }}</div>
                    <div class="status-value">{{ $ordersByStatus[$status] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

        <!-- Financial Overview -->
        <div class="financial-header">
            <h2 class="section-title">Financial Overview</h2>
            <div class="month-selector-container">
                <span class="month-selector-label">Select Month:</span>
                <select id="monthSelector" class="form-control">
                    @for ($i = 0; $i < 12; $i++)
                        @php
                            $monthDate = now()->subMonths($i);
                            $monthValue = $monthDate->format('Y-m');
                            $monthLabel = $monthDate->format('F Y');
                            $isSelected = $monthValue === (request('month') ?? now()->format('Y-m'));
                        @endphp
                        <option value="{{ $monthValue }}" {{ $isSelected ? 'selected' : '' }}>
                            {{ $monthLabel }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="financial-grid">
            <!-- Monthly Revenue Card -->
            <div class="financial-card bg-info">
                <div class="financial-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="financial-title">Monthly Revenue</div>
                <div class="financial-value money">${{ number_format($monthlyRevenue ?? ($currentMonthSales ?? 84520), 2) }}</div>
                <div class="financial-change {{ ($monthlyRevenueChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ ($monthlyRevenueChange ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                    {{ ($monthlyRevenueChange ?? 0) >= 0 ? '+' : '' }}{{ number_format(abs($monthlyRevenueChange ?? 12.5), 1) }}% vs last month
                </div>
            </div>

            <!-- Monthly Expenses Card -->
            <div class="financial-card bg-warning">
                <div class="financial-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="financial-title">Monthly Expenses</div>
                <div class="financial-value money">${{ number_format($monthlyExpenses ?? 42150, 2) }}</div>
                <div class="financial-change {{ ($monthlyExpenseChange ?? 0) <= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ ($monthlyExpenseChange ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                    {{ ($monthlyExpenseChange ?? 0) >= 0 ? '+' : '' }}{{ number_format(abs($monthlyExpenseChange ?? 8.2), 1) }}% vs last month
                </div>
            </div>

            <!-- Monthly Net Profit Card -->
            @php
                $monthlyProfit = $monthlyNetProfit ?? (($monthlyRevenue ?? ($currentMonthSales ?? 84520)) - ($monthlyExpenses ?? 42150));
                $profitChange = $monthlyProfitChange ?? 16.8;
            @endphp
            <div class="financial-card {{ $monthlyProfit >= 0 ? 'bg-success' : 'bg-danger' }}">
                <div class="financial-icon">
                    <i class="fas fa-{{ $monthlyProfit >= 0 ? 'trophy' : 'exclamation-triangle' }}"></i>
                </div>
                <div class="financial-title">Monthly Net Profit</div>
                <div class="financial-value money">${{ number_format($monthlyProfit, 2) }}</div>
                <div class="financial-change {{ $profitChange >= 0 ? 'text-success' : 'text-danger' }}">
                    @if($monthlyProfit >= 0)
                        <i class="fas fa-arrow-{{ $profitChange >= 0 ? 'up' : 'down' }}"></i> 
                        {{ $profitChange >= 0 ? '+' : '' }}{{ number_format(abs($profitChange), 1) }}% vs last month
                    @else
                        <i class="fas fa-exclamation-triangle"></i> Loss this month
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        @php
            $profitMargin = ($monthlyRevenue ?? ($currentMonthSales ?? 84520)) > 0 
                ? ($monthlyProfit / ($monthlyRevenue ?? ($currentMonthSales ?? 1))) * 100 
                : 0;
            $summaryBg = $monthlyProfit > 0 ? 'bg-success' : ($monthlyProfit < 0 ? 'bg-danger' : 'bg-warning');
        @endphp
        <div class="financial-summary {{ $summaryBg }}">
            <div class="summary-title">{{ now()->parse(request('month', now()->format('Y-m')))->format('F Y') }} Summary</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Revenue</span>
                    <span class="summary-value money">${{ number_format($monthlyRevenue ?? ($currentMonthSales ?? 84520), 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Expenses</span>
                    <span class="summary-value money">${{ number_format($monthlyExpenses ?? 42150, 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Net Profit</span>
                    <span class="summary-value money">${{ number_format($monthlyProfit, 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Profit Margin</span>
                    <span class="summary-value">{{ number_format($profitMargin, 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="content-grid">
            <!-- Recent Sales -->
            <div class="card">
                <div class="card-header">
                    <span>Recent Sales</span>
                    <button class="btn btn-primary btn-sm">View All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales ?? [] as $sale)
                                    <tr>
                                        <td><a href="#">#{{ $sale->id ?? $loop->index + 10480 }}</a></td>
                                        <td>{{ $sale->customer->name ?? 'Customer ' . ($loop->index + 1) }}</td>
                                        <td>{{ $sale->sale_date ?? now()->subDays($loop->index)->format('M j, Y') }}</td>
                                        <td class="money">${{ number_format($sale->total_amount ?? rand(500, 2500), 2) }}</td>
                                    </tr>
                                @empty
                                    @for ($i = 0; $i < 5; $i++)
                                        <tr>
                                            <td><a href="#">#{{ 10480 - $i }}</a></td>
                                            <td>Customer {{ $i + 1 }}</td>
                                            <td>{{ now()->subDays($i)->format('M j, Y') }}</td>
                                            <td class="money">${{ number_format(rand(500, 2500), 2) }}</td>
                                        </tr>
                                    @endfor
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock & Sales Goal -->
            <div>
                <!-- Low Stock Products -->
                <div class="card mb-4">
                    <div class="card-header">
                        <span>Low Stock Products</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lowStockProducts ?? [] as $product)
                                        <tr>
                                            <td><a href="#">{{ $product->name ?? 'Product ' . ($loop->index + 1) }}</a></td>
                                            <td><span class="badge bg-danger">{{ $product->quantity ?? rand(5, 20) }} {{ $product->unit ?? 'pcs' }}</span></td>
                                        </tr>
                                    @empty
                                        @for ($i = 0; $i < 4; $i++)
                                            <tr>
                                                <td><a href="#">Product {{ $i + 1 }}</a></td>
                                                <td><span class="badge bg-danger">{{ rand(5, 20) }} pcs</span></td>
                                            </tr>
                                        @endfor
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monthly Sales Goal -->
                @if (auth()->user()->role == 'admin')
                <div class="card">
                    <div class="card-header">
                        <span>Monthly Sales Goal</span>
                    </div>
                    <div class="card-body">
                        <div class="progress-container">
                            <div class="progress-label">
                                <span>Sales Progress</span>
                                <span>${{ number_format($currentMonthSales ?? 64520, 2) }} / ${{ number_format($monthlySalesGoal ?? 80000, 2) }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill bg-success" style="width: {{ $salesProgressPercentage ?? 80.65 }}%"></div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <small class="text-muted">{{ number_format($salesProgressPercentage ?? 80.65, 2) }}% of your monthly goal reached</small>
                        </div>
                        <form class="d-flex gap-2">
                            <input type="number" step="0.01" class="form-control form-control-sm" placeholder="New goal" value="{{ $monthlySalesGoal ?? 80000 }}" required>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                        @if (session('success'))
                            <div class="alert alert-success mt-2">{{ session('success') }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Low Stock Raw Materials -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Low Stock Raw Materials</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Current Quantity</th>
                                <th>Minimum Level</th>
                                <th>Unit</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockRawMaterials ?? [] as $material)
                                <tr>
                                    <td><a href="#">{{ $material->name ?? 'Material ' . ($loop->index + 1) }}</a></td>
                                    <td><span class="badge bg-danger">{{ $material->quantity ?? rand(10, 50) }}</span></td>
                                    <td>{{ $material->minimum_stock_level ?? rand(50, 100) }}</td>
                                    <td>{{ $material->unit ?? 'units' }}</td>
                                    <td>{{ $material->supplier->name ?? 'Supplier ' . ($loop->index + 1) }}</td>
                                </tr>
                            @empty
                                @for ($i = 0; $i < 3; $i++)
                                    <tr>
                                        <td><a href="#">Material {{ $i + 1 }}</a></td>
                                        <td><span class="badge bg-danger">{{ rand(10, 50) }}</span></td>
                                        <td>{{ rand(50, 100) }}</td>
                                        <td>units</td>
                                        <td>Supplier {{ $i + 1 }}</td>
                                    </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <div class="card">
                <div class="card-header">Monthly Sales vs Purchases (Last 12 Months)</div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesPurchasesBarChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Top Selling Products ({{ now()->parse(request('month', now()->format('Y-m')))->format('F Y') }})</div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topProductsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const months = @json(collect($months)->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M'))->values());
        const salesData = @json($salesTotals);
        const purchaseData = @json($purchaseTotals);
        const ctxBar = document.getElementById('salesPurchasesBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: 'Sales',
                        data: salesData,
                        backgroundColor: '#4361ee',
                        borderRadius: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 1,
                    },
                    {
                        label: 'Purchases',
                        data: purchaseData,
                        backgroundColor: '#f72585',
                        borderRadius: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    x: {
                        // stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        // stacked: true,
                        
                        grid: {
                            color: '#e5e7eb'
                        }
                    }
                }
            }
        });

        const topLabels = @json($topProductLabels);
        const topQty = @json($topProductQuantities);
        const ctxTopBar = document.getElementById('topProductsBarChart').getContext('2d');
        new Chart(ctxTopBar, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: topQty,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#eab308', '#f59e42', '#ef4444'
                    ],
                    borderRadius: 6,
                    barPercentage: 0.7,
                    // categoryPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        // grid: {
                        //     color: '#e5e7eb'
                        // }
                    }
                }
            }
        });
    });

  // Define the function FIRST (before event listener)
function updateFinancialMetrics() {
const monthSelector = document.getElementById('monthSelector');
const selectedMonth = monthSelector.value;

// Add loading state
monthSelector.disabled = true;

// Create URL with month parameter
const url = new URL(window.location.href);
url.searchParams.set('month', selectedMonth);

// Redirect to update the dashboard with selected month
window.location.href = url.toString();
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
const monthSelector = document.getElementById('monthSelector');

// Now updateFinancialMetrics is guaranteed to be defined
monthSelector.addEventListener('change', updateFinancialMetrics);
});
</script>
@endpush