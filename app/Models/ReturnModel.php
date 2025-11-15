<?php

namespace App\Models;

use Database\Factories\ReturnFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    use HasFactory;

    protected $table = 'returns';

    public $timestamps = false;

    protected $fillable = [
        'id_staff',
        'id_deliverer',
        'notes',
        'returned_at',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ReturnFactory::new();
    }

    public function deliverer()
    {
        return $this->belongsTo(User::class, 'id_deliverer');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }

    public function confirmations()
    {
        return $this->hasMany(ReturnConfirmation::class, 'id_return');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'return_items', 'id_return', 'id_item');
    }
}
