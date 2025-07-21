<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Map order statuses to Bootstrap badge color classes.
     */
    public const STATUS_BADGE_CLASSES = [
        'pending'    => 'warning',
        'processing' => 'primary',
        'shipped'    => 'info',
        'completed'  => 'success',
        'cancelled'  => 'danger',
    ];

    protected $fillable = [
        'customer_id',
        'order_date',
        'total_amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

        /**
     * Get the Bootstrap color class for the current status.
     */
    public function getBadgeClassAttribute(): string
    {
        return self::STATUS_BADGE_CLASSES[$this->status] ?? 'secondary';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
