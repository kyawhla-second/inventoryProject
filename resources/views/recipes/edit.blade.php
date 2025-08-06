@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Recipe: {{ $recipe->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('recipes.update', $recipe) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="product_id">Product *</label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                {{ (old('product_id') ?? $recipe->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Recipe Name *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') ?? $recipe->name }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="batch_size">Batch Size *</label>
                                    <input type="number" name="batch_size" id="batch_size" step="0.001" 
                                           class="form-control @error('batch_size') is-invalid @enderror" 
                                           value="{{ old('batch_size') ?? $recipe->batch_size }}" required>
                                    @error('batch_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="unit">Unit *</label>
                                    <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" 
                                           value="{{ old('unit') ?? $recipe->unit }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="yield_percentage">Yield Percentage *</label>
                                    <input type="number" name="yield_percentage" id="yield_percentage" step="0.01" 
                                           class="form-control @error('yield_percentage') is-invalid @enderror" 
                                           value="{{ old('yield_percentage') ?? $recipe->yield_percentage }}" required>
                                    @error('yield_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="preparation_time">Preparation Time (minutes)</label>
                                    <input type="number" name="preparation_time" id="preparation_time" 
                                           class="form-control @error('preparation_time') is-invalid @enderror" 
                                           value="{{ old('preparation_time') ?? $recipe->preparation_time }}">
                                    @error('preparation_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="production_time">Production Time (minutes)</label>
                                    <input type="number" name="production_time" id="production_time" 
                                           class="form-control @error('production_time') is-invalid @enderror" 
                                           value="{{ old('production_time') ?? $recipe->production_time }}">
                                    @error('production_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="version">Version *</label>
                                    <input type="text" name="version" id="version" class="form-control @error('version') is-invalid @enderror" 
                                           value="{{ old('version') ?? $recipe->version }}" required>
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') ?? $recipe->description }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="instructions">Instructions</label>
                            <textarea name="instructions" id="instructions" rows="4" 
                                      class="form-control @error('instructions') is-invalid @enderror">{{ old('instructions') ?? $recipe->instructions }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                   value="1" {{ (old('is_active') ?? $recipe->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Set as Active Recipe
                            </label>
                        </div>

                        <!-- Recipe Items -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Recipe Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="recipe-items">
                                    @foreach($recipe->recipeItems as $index => $item)
                                        <div class="recipe-item row mb-3">
                                            <div class="col-md-4">
                                                <select name="recipe_items[{{ $index }}][raw_material_id]" class="form-control" required>
                                                    <option value="">Select Raw Material</option>
                                                    @foreach($rawMaterials as $material)
                                                        <option value="{{ $material->id }}" 
                                                                {{ $item->raw_material_id == $material->id ? 'selected' : '' }}>
                                                            {{ $material->name }} ({{ $material->unit }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="recipe_items[{{ $index }}][quantity_required]" 
                                                       placeholder="Quantity" step="0.001" class="form-control" 
                                                       value="{{ $item->quantity_required }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="recipe_items[{{ $index }}][unit]" 
                                                       placeholder="Unit" class="form-control" 
                                                       value="{{ $item->unit }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="recipe_items[{{ $index }}][waste_percentage]" 
                                                       placeholder="Waste %" step="0.01" class="form-control"
                                                       value="{{ $item->waste_percentage }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-item">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="add-item" class="btn btn-secondary">Add Item</button>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update Recipe</button>
                            <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $recipe->recipeItems->count() }};
    
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('recipe-items');
        const newItem = document.querySelector('.recipe-item').cloneNode(true);
        
        // Update input names
        newItem.querySelectorAll('input, select').forEach(input => {
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
            if (document.querySelectorAll('.recipe-item').length > 1) {
                e.target.closest('.recipe-item').remove();
            }
        }
    });
});
</script>
@endsection