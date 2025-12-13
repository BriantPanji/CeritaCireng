<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemChangeLog extends Model
{
    protected $table = 'item_change_log';
    
    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'action',
        'field_changed',
        'old_value',
        'new_value',
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
     * Get action badge color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'CREATE' => 'green',
            'UPDATE' => 'blue',
            'DELETE' => 'red',
            'RESTORE' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Scope for filtering by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }
}
