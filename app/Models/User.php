<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factory_recovery_codes',
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

    public function hasOutlet()
    {
        return $this->belongsToMany(Outlet::class, 'staff_outlets', 'id_user', 'id_outlet');
    }

    public function attendance() {
        return $this->hasMany(Attendance::class, 'id_user');
    }

    public function todayAttendance() {
        return $this->hasOne(Attendance::class, 'id_user')->whereDate('attendance_date', now()->toDateString());
    }
    public function isTodayAttendance() {
        return $this->todayAttendance?->status ?? null;
    }
    public function hasAttendanceAt($date) {
        return $this->hasOne(Attendance::class, 'id_user')->whereDate('attendance_date', $date);
    }
    public function attendanceStatusAt($date) {
        return $this->hasAttendanceAt($date)?->status ?? null;
    }


    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }


    public function hasOtherExpense()
    {
        return $this->hasMany(OtherExpense::class);
    }

    public function deliveriesAsKurir()
    {
        return $this->hasMany(Delivery::class, 'id_courier');
    }

    public function deliveriesAsInventaris() {
        return $this->hasMany(Delivery::class, 'id_inventaris');
    }

    public function hasDeliveryConfirmation()
    {
        return $this->hasMany(DeliveryConfirmation::class);
    }

    public function hasDeliveryMistake()
    {
        return $this->hasMany(DeliveryMistake::class);
    }

    public function hasDeliveryMistakeConfirmation()
    {
        return $this->hasMany(DeliveryMistakeConfirmation::class);
    }


    public function hasReturnItem() {
        return $this->hasMany(ReturnModel::class, 'id_staff');
    }
    public function hasDeliverReturnItem() {
        return $this->hasMany(ReturnModel::class, 'id_deliverer');
    }

    public function hasReturnConfirmation()
    {
        return $this->hasMany(ReturnConfirmation::class);
    }
}
