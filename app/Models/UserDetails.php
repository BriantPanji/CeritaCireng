<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    // VIEW name
    protected $table = 'ccv_user_details';

    protected $primaryKey = 'user_id';

    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
