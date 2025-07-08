<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Enums\UserRoleEnum as EnumsUserRoleEnum;


class User extends Authenticatable implements
    JWTSubject,
    FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return url($this->image);
        }


        // Return external URL directly without using `url()`
        return url('images/default.webp');
    }



    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'dashboard') {
            return $this->role === "admin" || $this->role === "agent";
        }

        return true;
    }

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
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'points' => 0,
                ]);
            }
        });
    }
}
