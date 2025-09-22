<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'batch_size',
        'unit',
        'yield_percentage',
        'preparation_time',
        'production_time',
        'instructions',
        'is_active',
        'version',
        'created_by',
    ];

    protected $casts = [
        'batch_size' => 'decimal:3',
        'yield_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class)->orderBy('sequence_order');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function productionPlanItems()
    {
        return $this->hasMany(ProductionPlanItem::class);
    }

    public function getTotalMaterialCost()
    {
        return $this->recipeItems->sum(function ($item) {
            return $item->quantity_required * ($item->cost_per_unit ?? $item->rawMaterial->cost_per_unit);
        });
    }

    public function getEstimatedCostPerUnit()
    {
        return $this->batch_size > 0 ? $this->getTotalMaterialCost() / $this->batch_size : 0;
    }

    public function calculateMaterialRequirements($quantity)
    {
        $batchMultiplier = $quantity / $this->batch_size;
        
        return $this->recipeItems->map(function ($item) use ($batchMultiplier) {
            $requiredQuantity = $item->quantity_required * $batchMultiplier;
            $wasteQuantity = $requiredQuantity * ($item->waste_percentage / 100);
            $totalRequired = $requiredQuantity + $wasteQuantity;
            
            return [
                'raw_material_id' => $item->raw_material_id,
                'raw_material' => $item->rawMaterial,
                'required_quantity' => $requiredQuantity,
                'waste_quantity' => $wasteQuantity,
                'total_required' => $totalRequired,
                'unit' => $item->unit,
                'estimated_cost' => $totalRequired * ($item->cost_per_unit ?? $item->rawMaterial->cost_per_unit),
            ];
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
