<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $primaryKey = "userid";
    public $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'userid',
        'firstname',
        'lastname',
        'username',
        'institution',
        'gender',
        'email',
        'phone',
        'password',
        'last_login',
        'login_status',
        'status',
        'remember_token',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'last_login'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::disk("profile-photo")->url($value)
        );
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class, "userid");
    }
}
