@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{__('Dashboard')}}</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Products Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{__('Total Products')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{__('Total Customers')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Suppliers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{__('Total Suppliers')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSuppliers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                {{__('Total Orders')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders by Status Cards -->
        @foreach (['pending','processing','shipped','completed','cancelled'] as $status)
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-{{ App\Models\Order::STATUS_BADGE_CLASSES[$status] ?? 'secondary' }} shadow h-100 py-2">
                    <div class="card-body p-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            {{ ucfirst($status) }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersByStatus[$status] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Total Sales Amount Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{__('Total Sales')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">@money($totalSalesAmount)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Sales -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('Recent Sales')}}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Customer')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td><a href="{{ route('sales.show', $sale->id) }}">#{{ $sale->id }}</a></td>
                                        <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>@money($sale->total_amount)</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No recent sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('Low Stock Products')}}</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{__('Product')}}</th>
                                    <th>{{__('Stock')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockProducts as $product)
                                    <tr>
                                        <td><a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a></td>
                                        <td><span class="badge bg-danger">{{ $product->quantity }} {{ $product->unit }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">{{__('No products are low on stock')}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role == 'admin')
            <!-- Monthly Sales Goal -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('Monthly Sales Goal')}}</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">
                        {{__('Sales Progress')}}
                        <span class="float-end">@money($currentMonthSales) / @money($monthlySalesGoal)</span>
                    </h4>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $salesProgressPercentage }}%" aria-valuenow="{{ $salesProgressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-center mb-2">
                        <small>{{ number_format($salesProgressPercentage, 2) }}% of your monthly goal reached.</small>
                    </div>
                    <form action="{{ route('dashboard.goal') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="number" step="0.01" name="monthly_sales_goal" class="form-control form-control-sm me-2" placeholder="New goal" required>
                        <button type="submit" class="btn btn-sm btn-primary">{{__('Update')}}</button>
                    </form>
                    @if(session('success'))
                        <div class="alert alert-success mt-2 p-1 text-center">{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        @endif

    <!-- Low Stock Raw Materials -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">{{__('Low Stock Raw Materials')}}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">{{__('Name')}}</th>
                                    <th class="text-nowrap">{{__('Current Quantity')}}</th>
                                    <th class="text-nowrap">{{__('Minimum Level')}}</th>
                                    <th class="text-nowrap">{{__('Unit')}}</th>
                                    <th class="text-nowrap">{{__('Supplier')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockRawMaterials as $material)
                                    <tr>
                                        <td class="text-nowrap"><a href="{{ route('raw-materials.show', $material->id) }}">{{ $material->name }}</a></td>
                                        <td class="text-nowrap"><span class="badge bg-danger">{{ $material->quantity }}</span></td>
                                        <td class="text-nowrap">{{ $material->minimum_stock_level }}</td>
                                        <td class="text-nowrap">{{ $material->unit }}</td>
                                        <td class="text-nowrap">{{ $material->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{__('No raw materials are low on stock.')}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Monthly Sales Goal Row -->
    <div class="row mb-4 d-none">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{__('Monthly Sales Goal')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($monthlySalesGoal, 2) }}</div>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $salesProgressPercentage }}%" aria-valuenow="{{ $salesProgressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small>{{ number_format($salesProgressPercentage, 1) }}% achieved (${{ number_format($currentMonthSales,2) }})</small>
                            <form action="{{ route('dashboard.goal') }}" method="POST" class="mt-2 d-flex">
                                @csrf
                                <input type="number" step="0.01" name="monthly_sales_goal" class="form-control form-control-sm me-2" placeholder="New goal" required>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                            @if(session('success'))
                                <div class="alert alert-success mt-1 p-1 text-center">{{ session('success') }}</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header">{{__('Monthly Sales vs Purchases (Last 12 Months)')}}</div>
                <div class="card-body">
                    <canvas id="salesPurchasesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">{{__('Top Selling Products')}}</div>
                <div class="card-body">
                    <canvas id="topProductsChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Chart debug', {
            months: @json($months),
            salesTotals: @json($salesTotals),
            purchaseTotals: @json($purchaseTotals),
            topProductLabels: @json($topProductLabels),
            topProductQuantities: @json($topProductQuantities),
        });
        const months = @json($months->map(fn($m)=>\Carbon\Carbon::createFromFormat('Y-m', $m)->format('M'))->values());
        const salesData = @json($salesTotals);
        const purchaseData = @json($purchaseTotals);

        const ctxSP = document.getElementById('salesPurchasesChart').getContext('2d');
        new Chart(ctxSP, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sales',
                        data: salesData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        tension: 0.3,
                    },
                    {
                        label: 'Purchases',
                        data: purchaseData,
                        borderColor: '#eab308',
                        backgroundColor: 'rgba(234,179,8,0.2)',
                        tension: 0.3,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const topLabels = @json($topProductLabels);
        const topQty = @json($topProductQuantities);
        const ctxTop = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: topQty,
                    backgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
