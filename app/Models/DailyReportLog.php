<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportLog extends Model
{
    protected $table = 'daily_report_log';
    
    public $timestamps = false;

    protected $fillable = [
        'id_report',
        'action',
        'old_is_validated',
        'new_is_validated',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'old_is_validated' => 'boolean',
        'new_is_validated' => 'boolean',
    ];

    /**
     * Relationship to DailyOutletReport
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyOutletReport::class, 'id_report');
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'VALIDATED' => 'green',
            'INVALIDATED' => 'red',
            'UPDATED' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Scope for filtering by report
     */
    public function scopeByReport($query, $reportId)
    {
        return $query->where('id_report', $reportId);
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }
}
