@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Material Efficiency Report</h4>
                    <div>
                        <a href="{{ route('production-reports.material-efficiency.export', request()->all()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('production-reports.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date *</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ $request->start_date }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>End Date *</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ $request->end_date }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>Raw Material</label>
                                <select name="raw_material_id" class="form-control">
                                    <option value="">All Materials</option>
                                    @foreach($rawMaterials as $material)
                                        <option value="{{ $material->id }}" 
                                                {{ $request->raw_material_id == $material->id ? 'selected' : '' }}>
                                            {{ $material->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>

                    @if(isset($efficiencyData))
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>{{ $summary['total_materials'] }}</h5>
                                        <p class="mb-0">Materials Analyzed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>{{ number_format($summary['average_efficiency'], 1) }}%</h5>
                                        <p class="mb-0">Avg Efficiency</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>{{ number_format($summary['average_waste'], 1) }}%</h5>
                                        <p class="mb-0">Avg Waste</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5>${{ number_format($summary['total_waste_cost'], 2) }}</h5>
                                        <p class="mb-0">Total Waste Cost</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Efficiency Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Raw Material</th>
                                        <th>Total Usage</th>
                                        <th>Production</th>
                                        <th>Waste</th>
                                        <th>Testing</th>
                                        <th>Adjustment</th>
                                        <th>Other</th>
                                        <th>Efficiency %</th>
                                        <th>Waste %</th>
                                        <th>Total Cost</th>
                                        <th>Waste Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($efficiencyData as $data)
                                        <tr>
                                            <td>{{ $data['raw_material'] }}</td>
                                            <td>{{ number_format($data['total_usage'], 2) }} {{ $data['unit'] }}</td>
                                            <td>{{ number_format($data['production_usage'], 2) }}</td>
                                            <td class="text-danger">{{ number_format($data['waste_usage'], 2) }}</td>
                                            <td>{{ number_format($data['testing_usage'], 2) }}</td>
                                            <td>{{ number_format($data['adjustment_usage'], 2) }}</td>
                                            <td>{{ number_format($data['other_usage'], 2) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: {{ $data['efficiency_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                    <span class="text-success fw-bold">{{ number_format($data['efficiency_percentage'], 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                        <div class="progress-bar bg-danger" 
                                                             style="width: {{ $data['waste_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                    <span class="text-danger fw-bold">{{ number_format($data['waste_percentage'], 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>${{ number_format($data['total_cost'], 2) }}</td>
                                            <td class="text-danger">${{ number_format($data['waste_cost'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>TOTALS</th>
                                        <th>{{ number_format($efficiencyData->sum('total_usage'), 2) }}</th>
                                        <th>{{ number_format($efficiencyData->sum('production_usage'), 2) }}</th>
                                        <th>{{ number_format($efficiencyData->sum('waste_usage'), 2) }}</th>
                                        <th>{{ number_format($efficiencyData->sum('testing_usage'), 2) }}</th>
                                        <th>{{ number_format($efficiencyData->sum('adjustment_usage'), 2) }}</th>
                                        <th>{{ number_format($efficiencyData->sum('other_usage'), 2) }}</th>
                                        <th>{{ number_format($summary['average_efficiency'], 1) }}%</th>
                                        <th>{{ number_format($summary['average_waste'], 1) }}%</th>
                                        <th>${{ number_format($summary['total_cost'], 2) }}</th>
                                        <th>${{ number_format($summary['total_waste_cost'], 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($summary['highest_waste_material'])
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0">Highest Waste Material</h6>
                                        </div>
                                        <div class="card-body">
                                            <h5>{{ $summary['highest_waste_material']['raw_material'] }}</h5>
                                            <p class="text-danger mb-0">
                                                <strong>{{ number_format($summary['highest_waste_material']['waste_percentage'], 1) }}%</strong> waste rate
                                            </p>
                                            <small class="text-muted">
                                                ${{ number_format($summary['highest_waste_material']['waste_cost'], 2) }} in waste costs
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Most Efficient Material</h6>
                                        </div>
                                        <div class="card-body">
                                            <h5>{{ $summary['most_efficient_material']['raw_material'] }}</h5>
                                            <p class="text-success mb-0">
                                                <strong>{{ number_format($summary['most_efficient_material']['efficiency_percentage'], 1) }}%</strong> efficiency
                                            </p>
                                            <small class="text-muted">
                                                Only {{ number_format($summary['most_efficient_material']['waste_percentage'], 1) }}% waste
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection