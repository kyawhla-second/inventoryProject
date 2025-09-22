@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recipe Management</h4>
                    <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Recipe
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="is_active" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                                <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Recipes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Recipe Name</th>
                                    <th>Version</th>
                                    <th>Batch Size</th>
                                    <th>Status</th>
                                    <th>Est. Cost/Unit</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recipes as $recipe)
                                    <tr>
                                        <td>{{ $recipe->product->name }}</td>
                                        <td>{{ $recipe->name }}</td>
                                        <td>{{ $recipe->version }}</td>
                                        <td>{{ $recipe->batch_size }} {{ $recipe->unit }}</td>
                                        <td>
                                            <span class="badge {{ $recipe->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $recipe->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($recipe->getEstimatedCostPerUnit(), 2) }}</td>
                                        <td>{{ $recipe->createdBy->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('recipes.duplicate', $recipe) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No recipes found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $recipes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection