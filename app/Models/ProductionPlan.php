<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_number',
        'name',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'total_estimated_cost',
        'total_actual_cost',
        'notes',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'approved_at' => 'datetime',
        'total_estimated_cost' => 'decimal:2',
        'total_actual_cost' => 'decimal:2',
    ];

    public function productionPlanItems()
    {
        return $this->hasMany(ProductionPlanItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateMaterialRequirements()
    {
        $requirements = collect();
        
        foreach ($this->productionPlanItems as $planItem) {
            if ($planItem->recipe) {
                $itemRequirements = $planItem->recipe->calculateMaterialRequirements($planItem->planned_quantity);
                
                foreach ($itemRequirements as $requirement) {
                    $existing = $requirements->firstWhere('raw_material_id', $requirement['raw_material_id']);
                    
                    if ($existing) {
                        $requirements = $requirements->map(function ($item) use ($requirement) {
                            if ($item['raw_material_id'] === $requirement['raw_material_id']) {
                                $item['total_required'] += $requirement['total_required'];
                                $item['estimated_cost'] += $requirement['estimated_cost'];
                            }
                            return $item;
                        });
                    } else {
                        $requirements->push($requirement);
                    }
                }
            }
        }
        
        return $requirements;
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'approved' => 'bg-info',
            'in_progress' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-primary'
        };
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'in_progress']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->plan_number)) {
                $model->plan_number = 'PP-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
