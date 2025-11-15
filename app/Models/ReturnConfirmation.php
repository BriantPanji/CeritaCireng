<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnConfirmation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'return_confirmations';

    protected $fillable = [
        'id_return',
        'id_inventaris',
        'notes',
        'confirmed_at',
    ];

    public function inventaris()
    {
        return $this->belongsTo(User::class, 'id_inventaris');
    }

    public function returnModel()
    {
        return $this->belongsTo(ReturnModel::class, 'id_return');
    }
}
