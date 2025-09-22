<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_plan_id',
        'product_id',
        'recipe_id',
        'order_id',
        'planned_quantity',
        'actual_quantity',
        'unit',
        'estimated_material_cost',
        'actual_material_cost',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'priority',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:3',
        'actual_quantity' => 'decimal:3',
        'estimated_material_cost' => 'decimal:2',
        'actual_material_cost' => 'decimal:2',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function productionPlan()
    {
        return $this->belongsTo(ProductionPlan::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getVariancePercentage()
    {
        if ($this->planned_quantity == 0) return 0;
        return (($this->actual_quantity - $this->planned_quantity) / $this->planned_quantity) * 100;
    }

    public function getCostVariance()
    {
        return $this->actual_material_cost - $this->estimated_material_cost;
    }

    public function getCostVariancePercentage()
    {
        if ($this->estimated_material_cost == 0) return 0;
        return (($this->actual_material_cost - $this->estimated_material_cost) / $this->estimated_material_cost) * 100;
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-secondary',
            'in_progress' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-primary'
        };
    }

    public function getEfficiencyPercentage()
    {
        if ($this->planned_quantity == 0) return 0;
        return ($this->actual_quantity / $this->planned_quantity) * 100;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority');
    }
}
