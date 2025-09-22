<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Supplier;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'unit',
        'cost_per_unit',
        'supplier_id',
        'minimum_stock_level',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function usages()
    {
        return $this->hasMany(RawMaterialUsage::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock_level;
    }

    public function getTotalUsageForPeriod($startDate, $endDate)
    {
        return $this->usages()
            ->whereBetween('usage_date', [$startDate, $endDate])
            ->sum('quantity_used');
    }

    public function getTotalCostForPeriod($startDate, $endDate)
    {
        return $this->usages()
            ->whereBetween('usage_date', [$startDate, $endDate])
            ->sum('total_cost');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_raw_material')
                    ->withPivot([
                        'quantity_required',
                        'unit',
                        'cost_per_unit',
                        'waste_percentage',
                        'notes',
                        'is_primary',
                        'sequence_order'
                    ])
                    ->withTimestamps()
                    ->orderBy('product_raw_material.sequence_order');
    }

    public function primaryProducts()
    {
        return $this->products()->wherePivot('is_primary', true);
    }

    public function getProductsUsingThis()
    {
        return $this->products->map(function ($product) {
            return [
                'product' => $product,
                'quantity_required' => $product->pivot->quantity_required,
                'unit' => $product->pivot->unit,
                'is_primary' => $product->pivot->is_primary,
                'waste_percentage' => $product->pivot->waste_percentage,
            ];
        });
    }
}
