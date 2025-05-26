<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Laravel\Sanctum\HasApiTokens; // Import this

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uid',
        'date',
        'username',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'email',
        'campus',
        'role_id',
        'status',
        'phone',
        'loginlimit',
        'twofa',
        'photo',
        'disability_id',
        'password',
        'created_by',
        'last_activity',
        'default_workspace',
        'updated_at',
        'google2fa_secret',
        'two_fa_method',
        'two_fa_email_token',
        'campus_id',
        'isverifiedphone',
        'isverifiedemail',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'two_fa_email_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'last_activity' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Get full name
    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function hasTwoFactorEnabled()
    {
        return $this->twofa === 'YES';
    }

    public function hasLoginLimit()
    {
        return $this->loginlimit === 'YES';
    }

    public function isEmailVerified()
    {
        return $this->isverifiedemail === 'YES';
    }

    public function isPhoneVerified()
    {
        return $this->isverifiedphone === 'YES';
    }
}
