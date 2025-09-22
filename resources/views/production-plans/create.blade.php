@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Create Production Plan</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('production-plans.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Plan Name *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                           value="{{ old('description') }}">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="planned_start_date">Planned Start Date *</label>
                                    <input type="date" name="planned_start_date" id="planned_start_date" 
                                           class="form-control @error('planned_start_date') is-invalid @enderror" 
                                           value="{{ old('planned_start_date') }}" required>
                                    @error('planned_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="planned_end_date">Planned End Date *</label>
                                    <input type="date" name="planned_end_date" id="planned_end_date" 
                                           class="form-control @error('planned_end_date') is-invalid @enderror" 
                                           value="{{ old('planned_end_date') }}" required>
                                    @error('planned_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Production Plan Items -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Production Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="plan-items">
                                    <div class="plan-item row mb-3 p-3 border rounded">
                                        <div class="col-md-3">
                                            <label>Product *</label>
                                            <select name="plan_items[0][product_id]" class="form-control product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-recipe="{{ $product->activeRecipe?->id }}">
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quantity *</label>
                                            <input type="number" name="plan_items[0][planned_quantity]" 
                                                   placeholder="Quantity" step="0.001" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unit *</label>
                                            <input type="text" name="plan_items[0][unit]" 
                                                   placeholder="Unit" class="form-control" value="pcs" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Start Date *</label>
                                            <input type="date" name="plan_items[0][planned_start_date]" 
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>End Date *</label>
                                            <input type="date" name="plan_items[0][planned_end_date]" 
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label>Priority</label>
                                            <input type="number" name="plan_items[0][priority]" 
                                                   class="form-control" value="1" min="1" required>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <input type="hidden" name="plan_items[0][recipe_id]" class="recipe-id">
                                            <textarea name="plan_items[0][notes]" placeholder="Notes" 
                                                      class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove Item</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-item" class="btn btn-secondary">Add Item</button>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Production Plan</button>
                            <a href="{{ route('production-plans.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('plan-items');
        const newItem = document.querySelector('.plan-item').cloneNode(true);
        
        // Update input names
        newItem.querySelectorAll('input, select, textarea').forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            input.name = name;
            input.value = '';
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        container.appendChild(newItem);
        itemIndex++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            if (document.querySelectorAll('.plan-item').length > 1) {
                e.target.closest('.plan-item').remove();
            }
        }
    });

    // Auto-select recipe when product is selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const recipeId = selectedOption.getAttribute('data-recipe');
            const recipeInput = e.target.closest('.plan-item').querySelector('.recipe-id');
            recipeInput.value = recipeId || '';
        }
    });
});
</script>
@endsection