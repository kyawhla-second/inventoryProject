<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffDailyCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'charge_date',
        'daily_rate',
        'hours_worked',
        'overtime_hours',
        'overtime_rate',
        'total_charge',
        'notes',
        'status',
    ];

    protected $casts = [
        'charge_date' => 'date',
        'daily_rate' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_charge' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'user_id', 'user_id');
    }

    public function calculateTotalCharge()
    {
        $regularPay = $this->daily_rate * ($this->hours_worked / 8);
        $overtimePay = $this->overtime_hours * ($this->overtime_rate ?? $this->daily_rate * 1.5 / 8);
        
        $this->total_charge = $regularPay + $overtimePay;
        return $this->total_charge;
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('charge_date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    // API Methods for Mobile Frontend
    public static function getChargesForUser($userId, $startDate = null, $endDate = null, $status = null)
    {
        $query = self::where('user_id', $userId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('charge_date', [$startDate, $endDate]);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('charge_date', 'desc')->get();
    }
    
    public static function getSummaryForUser($userId, $startDate = null, $endDate = null)
    {
        $query = self::where('user_id', $userId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('charge_date', [$startDate, $endDate]);
        }
        
        $totalHours = $query->sum('hours_worked');
        $totalOvertimeHours = $query->sum('overtime_hours');
        $totalCharge = $query->sum('total_charge');
        $chargeCount = $query->count();
        
        return [
            'total_hours' => $totalHours,
            'total_overtime_hours' => $totalOvertimeHours,
            'total_charge' => $totalCharge,
            'charge_count' => $chargeCount
        ];
    }
    
    public static function updateChargeStatus($id, $status)
    {
        $charge = self::findOrFail($id);
        $charge->status = $status;
        $charge->save();
        return $charge;
    }
    
    public static function createBulkCharges($data)
    {
        $charges = [];
        
        foreach ($data as $chargeData) {
            $charge = new self($chargeData);
            $charge->calculateTotalCharge();
            $charge->save();
            $charges[] = $charge;
        }
        
        return $charges;
    }
}