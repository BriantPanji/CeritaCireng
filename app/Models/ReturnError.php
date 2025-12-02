<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnError extends Model
{
    use HasFactory;

    protected $table = 'return_errors';

    protected $fillable = [
        'id_return',
        'id_item',
        'id_staff',
        'wrong_quantity',
        'reason',
        'photo_path',
    ];

    public function return()
    {
        return $this->belongsTo(ReturnModel::class, 'id_return');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }
}
