@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('Add New Product')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('products.index') }}"> {{__('Back')}}</a>
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

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Name')}}:</strong>
                <input type="text" name="name" class="form-control" placeholder="Name">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Description')}}:</strong>
                <textarea class="form-control" style="height:150px" name="description" placeholder="Description"></textarea>
            </div>
        </div>
       
        
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Category')}}:</strong>
                <select name="category_id" class="form-control">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Quantity')}}:</strong>
                <input type="text" name="quantity" class="form-control" placeholder="Quantity">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Unit')}}:</strong>
                <select name="unit" class="form-control">
                    <option value="viss">{{ __('Viss (viss)') }}</option>
                    <option value="box">{{ __('Boxes (box)') }}</option>
                    <option value="pack">{{ __('Packs (pack)') }}</option>
                    
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Price')}}:</strong>
                <input type="text" name="price" class="form-control" placeholder="Price">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Cost:</strong>
                <input type="number" step="0" name="cost" class="form-control" placeholder="Cost">

            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{__('Minimum Stock Level')}}:</strong>
                <input type="number" name="minimum_stock_level" class="form-control" placeholder="Minimum Stock Level">
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
                    </div>
                    <button type="button" class="btn btn-success btn-sm" id="add-raw-material">
                        <i class="fas fa-plus"></i> {{__('Add Raw Material')}}
                    </button>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
        </div>
    </div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rawMaterialIndex = 1;
    
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
});
</script>
@endsection
