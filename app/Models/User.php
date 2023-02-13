<?php

namespace App\Models;

use App\Notifications\User\UserResetPassword;
use App\Notifications\User\UserVerifyEmailQueued;
use App\Traits\AuthLogs;
use App\Traits\HasEnum;
use App\Traits\Tenantable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Spatie\Permission\Traits\HasRoles;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Jenssegers\Agent\Agent;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, Tenantable, HasEnum, AuthenticationLoggable, AuthLogs;

    public const DT_ID = 'users_dataTable';
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
        'status',
        'password_changed_at',
    ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
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
     * Get web guard key
     * 
     * @return string
     */
    public static function GET_LOCK_KEY() {
      return 'VUEXY_WEB_LOCK_KEY';
    }

    public function getAvatarAttribute($value)
    {
      if(!$value)
      return asset('assets/img/avatars/'.substr($this->id, -1).'.png');
      return $value;
    }

  public function getFullNameAttribute()
  {
    return ucwords($this->first_name . ' ' . $this->last_name);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function sendEmailVerificationNotification()
  {
    $this->notify(new UserVerifyEmailQueued);
  }
  public function sendPasswordResetNotification($token)
  {
    $this->notify(new UserResetPassword($token));
  }
}
