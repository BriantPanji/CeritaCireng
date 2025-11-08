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
            'email_verified_at' => 'datetime',
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
    public function hasRoles()
    {
        return $this->hasMany(Role::class);
    }

    public function hasOutlet()
    {
        return $this->belongsToMany(Outlet::class, 'staff_outlets', 'id_user', 'id_outlet');
    }

    public function hasAttend()
    {
        return $this->belongsToMany(Attendance::class, 'user_attendances', 'id_user', 'id_attendance');
    }

    public function hasExpense()
    {
        return $this->hasMany(OtherExpense::class);
    }

    public function deliveryCourier()
    {
        return $this->hasMany(Delivery::class, 'id_kurir');
    }

    public function deliveryInventory()
    {
        return $this->hasMany(Delivery::class, 'id_inventaris');
    }

    public function hasDeliveryConfirmation()
    {
        return $this->belongsToMany(Delivery::class, 'delivery_confirmations', 'id_staff', 'id_delivery');
    }

    public function hasDeliveryMistake()
    {
        return $this->hasOne(DeliveryMistake::class);
    }

    public function hasDeliveryMistakeConfirmation()
    {
        return $this->belongsToMany(DeliveryMistake::class, 'delivery_mistake_confirmation', 'id_inventaris', 'id_delivery_mistake');
    }

    public function hasReturnAsCourier(){
        return $this->hasMany(ReturnModel::class, 'id_deliverer');
    }
    public function hasReturnAsStaff(){
        return $this->hasMany(ReturnModel::class, 'id_staff');
    }

    public function hasReturnConfirm(){
        return $this->belongsToMany(ReturnModel::class, 'return_confirmation', 'id_inventaris', 'id_return');
    }
}
