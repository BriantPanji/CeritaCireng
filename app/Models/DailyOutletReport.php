<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyOutletReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_outlet',
        'id_staff',
        'report_date',
        'report_time',
        'is_validated',
        'notes',
        'created_by_name',
        'outlet_name',
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_time' => 'datetime:H:i:s',
        'is_validated' => 'boolean',
    ];

    // Relationships
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyOutletReportItem::class, 'id_outlet_report');
    }


    // Computed properties
    public function getTotalExpenseAttribute(): int
    {
        // Total expense = sum of (item_cost * qty_sold) for all items
        return $this->items->sum(function ($item) {
            return $item->item_cost * $item->qty_sold;
        });
    }

    // Scopes
    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopeInvalidated($query)
    {
        return $query->where('is_validated', false);
    }

    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('report_date', $date);
    }

    /**
     * Get the latest valid report for an outlet on a specific date
     */
    public static function getLatestValid(int $outletId, string $date): ?self
    {
        return static::where('id_outlet', $outletId)
            ->whereDate('report_date', $date)
            ->where('is_validated', true)
            ->first();
    }

    /**
     * Get all versions (history) of reports for the same outlet and date
     */
    public function getVersionHistory()
    {
        return static::where('id_outlet', $this->id_outlet)
            ->whereDate('report_date', $this->report_date)
            ->orderBy('id', 'desc')
            ->get();
    }
}
