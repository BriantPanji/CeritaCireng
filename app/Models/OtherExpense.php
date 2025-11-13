<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherExpense extends Model
{
    use HasFactory;

    public function expenseBy()
    {
        return $this->belongsTo(User::class);
    }
}
