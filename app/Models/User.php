<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

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

    public function setGoogleTokenAttribute($value)
    {
        $this->attributes['google_token'] = $value
            ? Crypt::encryptString($value)
            : null;
    }

    public function getGoogleTokenAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setGoogleRefreshTokenAttribute($value)
    {
        $this->attributes['google_refresh_token'] = $value
            ? Crypt::encryptString($value)
            : null;
    }

    public function getGoogleRefreshTokenAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }
}
