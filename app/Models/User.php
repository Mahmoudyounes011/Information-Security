<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'type_user',
        'password',
        'car_num',
        'phone_num',
        'session_key',
        'iv',
        'balance',
    ];
    protected $casts = [
        'balance' => 'decimal:2',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function setBalanceAttribute($value)
    {
        return Crypt::encryptString($value);
    }

    public static function getBalanceAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function clientKey()
    {
        return $this->hasOne(ClientKey::class);
    }
}
