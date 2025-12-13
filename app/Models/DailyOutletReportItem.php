<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyOutletReportItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_outlet_report',
        'id_item',
        'item_name',
        'item_cost',
        'item_unit',
        'initial_stock',
        'stock_delivered',
        'stock_returned',
        'qty_damaged',
        'stock_remained',
        'qty_sold',
        'total_expense',
    ];

    protected $casts = [
        'initial_stock' => 'integer',
        'stock_delivered' => 'integer',
        'stock_returned' => 'integer',
        'qty_damaged' => 'integer',
        'stock_remained' => 'integer',
        'qty_sold' => 'integer',
        'total_expense' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyOutletReport::class, 'id_outlet_report');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    // Computed properties
    public function getStockUsedAttribute(): int
    {
        return $this->qty_sold + $this->qty_damaged;
    }

    public function getStockBalanceAttribute(): int
    {
        return $this->initial_stock + $this->stock_delivered -
            $this->qty_sold - $this->qty_damaged - $this->stock_returned;
    }
}
