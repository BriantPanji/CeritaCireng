<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable = ['name', 'day_number'];

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_closed_days');
    }
}