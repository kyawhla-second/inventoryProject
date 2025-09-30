@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('Edit Product') }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('products.index') }}"> {{ __('Back') }}</a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Name') }}:</strong>
                    <input type="text" name="name" value="{{ $product->name }}" class="form-control"
                        placeholder="Name">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Description') }}:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ $product->description }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Barcode') }}:</strong>
                    <input type="text" name="barcode" value="{{ $product->barcode }}" class="form-control"
                        placeholder="Barcode">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Image') }}:</strong>
                    <input type="file" name="image" class="form-control">
                    <img src="/images/{{ $product->image }}" width="100px" class="mt-2">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Category') }}:</strong>
                    <select name="category_id" class="form-control">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Quantity') }}:</strong>
                    <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control"
                        placeholder="Quantity">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Unit') }}:</strong>
                    <select name="unit" class="form-control">
                        <option value="oz" {{ $product->unit == 'viss' ? 'selected' : '' }}>{{ __('viss(viss)') }}
                        </option>
                        <option value="box" {{ $product->unit == 'box' ? 'selected' : '' }}>{{ __('Boxes (box)') }}
                        </option>
                        <option value="pack" {{ $product->unit == 'pack' ? 'selected' : '' }}>{{ __('Packs (pack)') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Price') }}:</strong>
                    <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control"
                        placeholder="Price">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Cost') }}:</strong>
                    <input type="number" step="0.01" name="cost" value="{{ $product->cost }}" class="form-control"
                        placeholder="Cost">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{ __('Minimum Stock Level') }}:</strong>
                    <input type="number" name="minimum_stock_level" value="{{ $product->minimum_stock_level }}"
                        class="form-control" placeholder="Minimum Stock Level">
                </div>
            </div>

            <!-- Raw Materials Section -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{__('Raw Materials')}} ({{__('Optional')}})</h5>
                    </div>
                    <div class="card-body">
                        <div id="raw-materials-container">
                            @if($product->rawMaterials->count() > 0)
                                @foreach($product->rawMaterials as $index => $rawMaterial)
                                    <div class="raw-material-item border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>{{__('Raw Material')}}</label>
                                                <select name="raw_materials[{{ $index }}][raw_material_id]" class="form-control raw-material-select">
                                                    <option value="">{{__('Select Raw Material')}}</option>
                                                    @foreach($rawMaterials as $material)
                                                        <option value="{{ $material->id }}" 
                                                                data-unit="{{ $material->unit }}" 
                                                                data-cost="{{ $material->cost_per_unit }}"
                                                                {{ $rawMaterial->id == $material->id ? 'selected' : '' }}>
                                                            {{ $material->name }} ({{ $material->unit }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{__('Quantity Required')}}</label>
                                                <input type="number" name="raw_materials[{{ $index }}][quantity_required]" class="form-control" 
                                                       step="0.001" value="{{ $rawMaterial->pivot->quantity_required }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{__('Unit')}}</label>
                                                <input type="text" name="raw_materials[{{ $index }}][unit]" class="form-control unit-input" 
                                                       value="{{ $rawMaterial->pivot->unit }}" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{__('Cost per Unit')}}</label>
                                                <input type="number" name="raw_materials[{{ $index }}][cost_per_unit]" class="form-control cost-input" 
                                                       step="0.01" value="{{ $rawMaterial->pivot->cost_per_unit ?? $rawMaterial->cost_per_unit }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{__('Waste %')}}</label>
                                                <input type="number" name="raw_materials[{{ $index }}][waste_percentage]" class="form-control" 
                                                       step="0.01" value="{{ $rawMaterial->pivot->waste_percentage }}" min="0" max="100">
                                            </div>
                                            <div class="col-md-1">
                                                <label>{{__('Primary')}}</label>
                                                <div class="form-check">
                                                    <input type="checkbox" name="raw_materials[{{ $index }}][is_primary]" class="form-check-input" value="1"
                                                           {{ $rawMaterial->pivot->is_primary ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-11">
                                                <label>{{__('Notes')}}</label>
                                                <input type="text" name="raw_materials[{{ $index }}][notes]" class="form-control" 
                                                       value="{{ $rawMaterial->pivot->notes }}" placeholder="{{__('Optional notes')}}">
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-raw-material">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="raw-material-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>{{__('Raw Material')}}</label>
                                            <select name="raw_materials[0][raw_material_id]" class="form-control raw-material-select">
                                                <option value="">{{__('Select Raw Material')}}</option>
                                                @foreach($rawMaterials as $material)
                                                    <option value="{{ $material->id }}" 
                                                            data-unit="{{ $material->unit }}" 
                                                            data-cost="{{ $material->cost_per_unit }}">
                                                        {{ $material->name }} ({{ $material->unit }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{__('Quantity Required')}}</label>
                                            <input type="number" name="raw_materials[0][quantity_required]" class="form-control" 
                                                   step="0.001" placeholder="0.000">
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{__('Unit')}}</label>
                                            <input type="text" name="raw_materials[0][unit]" class="form-control unit-input" 
                                                   placeholder="kg" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{__('Cost per Unit')}}</label>
                                            <input type="number" name="raw_materials[0][cost_per_unit]" class="form-control cost-input" 
                                                   step="0.01" placeholder="0.00">
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{__('Waste %')}}</label>
                                            <input type="number" name="raw_materials[0][waste_percentage]" class="form-control" 
                                                   step="0.01" value="0" min="0" max="100">
                                        </div>
                                        <div class="col-md-1">
                                            <label>{{__('Primary')}}</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="raw_materials[0][is_primary]" class="form-check-input" value="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-11">
                                            <label>{{__('Notes')}}</label>
                                            <input type="text" name="raw_materials[0][notes]" class="form-control" 
                                                   placeholder="{{__('Optional notes')}}">
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-raw-material" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="add-raw-material">
                            <i class="fas fa-plus"></i> {{__('Add Raw Material')}}
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
        </div>

    </form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rawMaterialIndex = {{ $product->rawMaterials->count() }};
    
    // Add raw material row
    document.getElementById('add-raw-material').addEventListener('click', function() {
        const container = document.getElementById('raw-materials-container');
        const template = container.querySelector('.raw-material-item').cloneNode(true);
        
        // Update all input names and IDs with new index
        template.querySelectorAll('input, select').forEach(function(input) {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, '[' + rawMaterialIndex + ']'));
            }
            input.value = '';
        });
        
        // Show remove button for all items except the first
        template.querySelector('.remove-raw-material').style.display = 'block';
        
        container.appendChild(template);
        rawMaterialIndex++;
        
        // Update remove button visibility
        updateRemoveButtonVisibility();
    });
    
    // Remove raw material row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-raw-material')) {
            e.target.closest('.raw-material-item').remove();
            updateRemoveButtonVisibility();
        }
    });
    
    // Handle raw material selection change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('raw-material-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const unitInput = e.target.closest('.raw-material-item').querySelector('.unit-input');
            const costInput = e.target.closest('.raw-material-item').querySelector('.cost-input');
            
            if (selectedOption.value) {
                unitInput.value = selectedOption.dataset.unit || '';
                costInput.value = selectedOption.dataset.cost || '';
            } else {
                unitInput.value = '';
                costInput.value = '';
            }
        }
    });
    
    function updateRemoveButtonVisibility() {
        const items = document.querySelectorAll('.raw-material-item');
        items.forEach(function(item, index) {
            const removeBtn = item.querySelector('.remove-raw-material');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
    
    // Initialize remove button visibility
    updateRemoveButtonVisibility();
});
</script>
@endsection
