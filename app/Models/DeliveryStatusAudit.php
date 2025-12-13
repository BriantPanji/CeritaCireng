<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryStatusAudit extends Model
{
    protected $table = 'delivery_status_audit';
    
    public $timestamps = false;

    protected $fillable = [
        'id_delivery',
        'old_status',
        'new_status',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Relationship to Delivery
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'id_delivery');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->new_status) {
            'SELESAI' => 'green',
            'DIKIRIM' => 'blue',
            'DITUGASKAN' => 'yellow',
            'DIBATALKAN' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope for filtering by delivery
     */
    public function scopeByDelivery($query, $deliveryId)
    {
        return $query->where('id_delivery', $deliveryId);
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }
}
