<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'raw_material_id',
        'quantity_required',
        'unit',
        'cost_per_unit',
        'waste_percentage',
        'notes',
        'sequence_order',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getTotalCost()
    {
        $costPerUnit = $this->cost_per_unit ?? $this->rawMaterial->cost_per_unit;
        return $this->quantity_required * $costPerUnit;
    }

    public function getQuantityWithWaste()
    {
        return $this->quantity_required * (1 + ($this->waste_percentage / 100));
    }

    public function getTotalCostWithWaste()
    {
        $costPerUnit = $this->cost_per_unit ?? $this->rawMaterial->cost_per_unit;
        return $this->getQuantityWithWaste() * $costPerUnit;
    }
}
