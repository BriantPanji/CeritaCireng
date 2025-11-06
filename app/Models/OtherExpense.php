<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherExpense extends Model
{
    public function expenseBy()
    {
        return $this->belongsTo(User::class);
    }
}
