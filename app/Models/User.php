<?php

namespace App\Models;

use App\Notifications\User\UserResetPassword;
use App\Notifications\User\UserVerifyEmailQueued;
use App\Traits\AuthLogs;
use App\Traits\HasEnum;
use App\Traits\Tenantable;
use App\Traits\TenancyScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Avatar;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements MustVerifyEmail, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, HasEnum, AuthenticationLoggable, AuthLogs, TenancyScope;
    use \OwenIt\Auditing\Auditable;

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
        'email_verified_at',
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

    // public static function boot()
    // {
    //   parent::boot();
    //   static::addGlobalScope(new TenancyScope(Auth::user()));
    // }

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
      return Avatar::create($this->full_name)->toBase64();
      return $value;
    }

    public function getFullNameAttribute()
    {
      return ucwords($this->first_name. ' ' . $this->last_name);
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

    /**
     * User has many morph fields of password history
     *
     * @return MorphMany
     */
    public function passwordHistories()
    {
      return $this->morphMany(PasswordHistory::class, 'authable');
    }
}
