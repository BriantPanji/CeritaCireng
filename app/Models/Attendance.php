<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public function attendedBy()
    {
        return $this->belongsToMany(User::class, 'user_attendances', 'id_attendance', 'id_user');
    }
}
