<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'image',
        'quantity',
        'unit',
        'price',
        'cost',
        'minimum_stock_level',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rawMaterialUsages()
    {
        return $this->hasMany(RawMaterialUsage::class);
    }
}
