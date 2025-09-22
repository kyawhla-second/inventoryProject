@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Production Reports</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                    <h5>Variance Analysis</h5>
                                    <p class="text-muted">Compare planned vs actual production quantities and costs</p>
                                    <a href="{{ route('production-reports.variance-analysis') }}" class="btn btn-primary">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-recycle fa-3x text-success mb-3"></i>
                                    <h5>Material Efficiency</h5>
                                    <p class="text-muted">Track waste percentages and material efficiency metrics</p>
                                    <a href="{{ route('production-reports.material-efficiency') }}" class="btn btn-success">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-industry fa-3x text-info mb-3"></i>
                                    <h5>Production Summary</h5>
                                    <p class="text-muted">Overall production performance and statistics</p>
                                    <a href="{{ route('production-reports.production-summary') }}" class="btn btn-info">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-3x text-warning mb-3"></i>
                                    <h5>Cost Analysis</h5>
                                    <p class="text-muted">Detailed cost breakdown and analysis by product</p>
                                    <a href="{{ route('production-reports.cost-analysis') }}" class="btn btn-warning">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Stats</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border-right">
                                                <h3 class="text-primary">{{ \App\Models\ProductionPlan::count() }}</h3>
                                                <p class="text-muted mb-0">Total Plans</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-right">
                                                <h3 class="text-success">{{ \App\Models\ProductionPlan::where('status', 'completed')->count() }}</h3>
                                                <p class="text-muted mb-0">Completed</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-right">
                                                <h3 class="text-warning">{{ \App\Models\ProductionPlan::where('status', 'in_progress')->count() }}</h3>
                                                <p class="text-muted mb-0">In Progress</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h3 class="text-info">{{ \App\Models\Recipe::where('is_active', true)->count() }}</h3>
                                            <p class="text-muted mb-0">Active Recipes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection