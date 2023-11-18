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
use Illuminate\Support\Facades\Storage;
use Modules\Core\Contracts\Localizeable;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

class User extends Authenticatable implements MustVerifyEmail, Auditable, Localizeable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, HasEnum, AuthenticationLoggable, AuthLogs, TenancyScope;
    use \OwenIt\Auditing\Auditable;
    use MustVerifyNewEmail;

    public const DT_ID = 'users_dataTable';
    public const AVATAR_PATH = 'users-avatars';
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
        'avatar',
        'country_id',
        'state_id',
        'city_id',
        'gender',
        'job_title',
        'is_primary',
        'can_login',
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

    protected static function boot(): void
  {
      parent::boot();

      static::updating(function ($model) {
          if($model->isDirty('email')) {
            $model->email_update_logs()->create([
              'old_email' => $model->getOriginal('email'),
              'new_email' => $model->email,
            ]);
          }
      });
  }

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
      if (!$value)
      return Avatar::create($this->full_name)->toBase64();
    return @Storage::url($value);
    }

    public function isSuperAdmin()
    {
      return false;
    }

    /**
     * Get the user time format
     */
    public function getLocalTimeFormat(): string
    {
        return $this->time_format ? $this->time_format : 'H:i';
    }

    /**
     * Get the user date format
     */
    public function getLocalDateFormat(): string
    {
        return $this->date_format ? $this->date_format : 'F j, Y';
    }

    /**
     * Get the user timezone
     */
    public function getUserTimezone(): string
    {
        return $this->timezone ? $this->timezone : 'Asia/Karachi';
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
     * User has many morph fields of Device Authorized
     *
     * @return MorphMany
     */
    public function deviceAuthorizations()
    {
      return $this->morphMany(DeviceAuthorization::class, 'authenticatable');
    }


    public function addDeviceAuthorization(array $attributes)
    {

        $ip          = $attributes['ip_address'];
        $userAgent   = $attributes['user_agent'];
        $fingerprint = $attributes['fingerprint'];

        $deviceAuthorization = $this->deviceAuthorizations()
          ->where('fingerprint', $fingerprint)
          ->whereIpAddress($ip)
          ->whereUserAgent($userAgent)
          ->whereFingerprint($fingerprint)
          ->whereSafe(true)
          ->latest('created_at')
            ->firstOrNew();

        if ($deviceAuthorization->exists) {
            if ($deviceAuthorization->isDeviceAuthorized()) {
                $deviceAuthorization->attempts += 1;
                $deviceAuthorization->updated_at = now();
               //$deviceAuthorization->fill($attributes);
               $deviceAuthorization->safe = true;
                $deviceAuthorization->save();
            } else {
                $deviceAuthorization->safe = false;
                $deviceAuthorization->save();
                return false;
            }
        } else {
            $deviceAuthorization->fill($attributes);
            $deviceAuthorization->safe = true;
            $deviceAuthorization->save();
        }

        return true;
    }

    public function shouldSkipTwoFactor($ip,$userAgent,$fingerprint)
    {

        $deviceAuthorization = $this->deviceAuthorizations()
            ->where('fingerprint', $fingerprint)
            ->whereIpAddress($ip)
            ->whereUserAgent($userAgent)
            ->whereFingerprint($fingerprint)
            ->latest('created_at')
            ->first();

        if (!$deviceAuthorization) {
            return false;
        }

        $safe = $deviceAuthorization->safe;
        $lastLogin = $deviceAuthorization->updated_at;
        $expiration = now()->subDays(30);
//dd($deviceAuthorization->failed_attempts < config('auth.device_authorization.failed_limit'));
        return $safe && $lastLogin->gt($expiration) &&  $deviceAuthorization->failed_attempts < config('auth.device_authorization.failed_limit');
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

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function email_update_logs()
    {
      return $this->morphMany(EmailUpdateLog::class, 'user');
    }
}
