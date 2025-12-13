<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryChangeLog extends Model
{
    protected $table = 'inventory_change_log';
    
    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'old_stock',
        'new_stock',
        'change_amount',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Relationship to Item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    /**
     * Accessor for formatted change_amount with +/- sign
     */
    public function getFormattedChangeAttribute(): string
    {
        $amount = $this->change_amount;
        return $amount > 0 ? "+{$amount}" : (string)$amount;
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('timestamp', [$start, $end]);
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }
}
