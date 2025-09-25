<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Purchase;
use App\Models\Product;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'raw_material_id',
        'quantity',
        'unit_cost',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

        /**
     * Get the cost attribute (alias for unit_cost)
     */
    public function getCostAttribute(): float
    {
        return $this->unit_cost;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
