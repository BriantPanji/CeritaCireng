<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'display_name',
        'username',
        'phone',
        'role_id',
        'outlet_id',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'staff_outlets', 'id_user', 'id_outlet');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'id_user');
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class, 'id_user')->whereDate('attendance_date', now()->toDateString());
    }

    public function isTodayAttendance()
    {
        return $this->todayAttendance()->value('status');
    }

    public function attendanceAt($date)
    {
        return $this->hasOne(Attendance::class, 'id_user')->whereDate('attendance_date', $date);
    }

    public function attendanceStatusAt($date)
    {
        return $this->attendanceAt($date)->value('status');
    }


    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }


    public function otherExpenses()
    {
        return $this->hasMany(OtherExpense::class, 'id_staff');
    }

    public function deliveriesAsKurir()
    {
        return $this->hasMany(Delivery::class, 'id_kurir');
    }

    public function deliveriesAsInventaris() {
        return $this->hasMany(Delivery::class, 'id_inventaris');
    }

    public function deliveryConfirmations()
    {
        return $this->hasMany(DeliveryConfirmation::class, 'id_staff');
    }

    public function deliveryMistakes()
    {
        return $this->hasMany(DeliveryMistake::class, 'id_staff');
    }

    public function deliveryMistakeConfirmations()
    {
        return $this->hasMany(DeliveryMistakeConfirmation::class, 'id_staff');
    }

    public function returns()
    {
        return $this->hasMany(ReturnModel::class, 'id_staff');
    }

    public function deliveredReturns()
    {
        return $this->hasMany(ReturnModel::class, 'id_deliverer');
    }

    public function returnConfirmations()
    {
        return $this->hasMany(ReturnConfirmation::class, 'id_staff');
    }
}
