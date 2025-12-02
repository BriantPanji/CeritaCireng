<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'return_items'; // pastikan sesuai nama tabel

    protected $fillable = [
        'id_return',
        'id_item',
        'quantity',
    ];

    public $timestamps = false; // kalau tabel kamu tidak punya created_at, updated_at
}
