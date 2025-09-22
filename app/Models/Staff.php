<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'hire_date',
        'position',
        'department',
        'base_salary',
        'hourly_rate',
        'overtime_rate',
        'employment_type',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'profile_photo',
        'user_id',
        'supervisor_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Staff::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Staff::class, 'supervisor_id');
    }

    public function dailyCharges()
    {
        return $this->hasMany(StaffDailyCharge::class, 'user_id', 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'inactive' => 'bg-secondary',
            'terminated' => 'bg-danger',
            'on_leave' => 'bg-warning',
            default => 'bg-primary'
        };
    }

    public function getEmploymentTypeBadgeClassAttribute()
    {
        return match($this->employment_type) {
            'full_time' => 'bg-primary',
            'part_time' => 'bg-info',
            'contract' => 'bg-warning',
            'temporary' => 'bg-secondary',
            default => 'bg-primary'
        };
    }

    public function getTotalChargesForPeriod($startDate, $endDate)
    {
        return $this->dailyCharges()
            ->whereBetween('charge_date', [$startDate, $endDate])
            ->sum('total_charge');
    }

    public function getWorkedHoursForPeriod($startDate, $endDate)
    {
        return $this->dailyCharges()
            ->whereBetween('charge_date', [$startDate, $endDate])
            ->sum('hours_worked');
    }

    public function getOvertimeHoursForPeriod($startDate, $endDate)
    {
        return $this->dailyCharges()
            ->whereBetween('charge_date', [$startDate, $endDate])
            ->sum('overtime_hours');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->employee_id)) {
                $lastStaff = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastStaff ? $lastStaff->id + 1 : 1;
                $model->employee_id = 'EMP-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
