<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use App\Traits\HasEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, Impersonate, HasEnum;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'status'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function getAvatarAttribute($value)
    {
      if(!$value)
      return asset('assets/img/avatars/'.substr($this->id, -1).'.png');
      return $value;
    }

    public function getFullNameAttribute()
    {
      return ucwords($this->first_name. ' ' . $this->last_name);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail); // my notification
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function canBeImpersonated()
    {
        return $this->id != 1;
    }

    public function programs()
    {
      return $this->belongsToMany(Program::class)->using(ProgramsUser::class);;
    }

}
