<?php

namespace App\Models;

use Modules\Core\Concerns\HasMeta;
use Modules\Core\Contracts\Metable;
use App\Notifications\Admin\VerifyEmail;
use App\Notifications\Admin\ResetPassword;
use App\Traits\AuthLogs;
use App\Traits\HasEnum;
use App\Traits\Approval\ApprovesChanges;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;
use Avatar;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasPermissions;
use Modules\Core\Contracts\Localizeable;
use Modules\MailClient\Models\EmailAccount;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;
use Spatie\Comments\Models\Concerns\InteractsWithComments;
use Spatie\Comments\Models\Concerns\Interfaces\CanComment;

class Admin extends Authenticatable implements MustVerifyEmail, Metable, Auditable, Localizeable, CanComment
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, Impersonate, HasEnum, AuthenticationLoggable, AuthLogs, HasPermissions;
  use \OwenIt\Auditing\Auditable;
  use HasMeta, ApprovesChanges;
  use MustVerifyNewEmail;
  use InteractsWithComments;
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'avatar',
    'phone',
    'email',
    'last_seen',
    'is_online',
    'password',
    'status',
    'designation_id',
    'password_changed_at',
    'email_verified_at',
    'country_id',
    'state_id',
    'city_id',
  ];

  public const AVATAR_PATH = 'admins-avatars';
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
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
      'is_online',
      'last_seen',
  ];

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

  /**
   * Get admin guard key
   *
   * @return string
   */
  public static function GET_LOCK_KEY()
  {
    return 'VUEXY_ADMIN_LOCK_KEY';
  }

  public function isSuperAdmin(): bool
  {
    return $this->id == 1;
  }

  /**
   * Send the email verification notification.
   *
   * @return void
   */
  public function getAvatarAttribute($value)
  {
    if (!$value)
      return Avatar::create($this->full_name)->toBase64();
    return @Storage::url($value);
  }

  public function getPhotoUrlAttribute()
  {
    return $this->avatar;
  }

  public function getNameAttribute()
  {
    return $this->full_name;
  }

  public function getFullNameAttribute()
  {
    return ucwords($this->first_name . ' ' . $this->last_name);
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

  public function authorizedToApproveOrDisapprove(\Approval\Models\Modification $mod): bool
  {
    if (isset($mod->modifications['company_id']['modified'])) {
      $company_id = $mod->modifications['company_id']['modified'];
    } else {
      $company_id = $mod->modifiable->company_id;
    }
    $company = Company::find($company_id);
    $level = ApprovalLevel::pluck('id')->toArray()[$company->approval_level - 1]; // get the current level id
    $approver = ApprovalLevelApprover::where('workflow_level_id', $level)->where('user_id', auth()->id())->first();

    return $approver ? true : false;
  }

  protected function authorizedToApprove(\Approval\Models\Modification $mod): bool
  {
    return $this->authorizedToApproveOrDisapprove($mod);
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

        return $safe && $lastLogin->gt($expiration) &&  $deviceAuthorization->failed_attempts < config('auth.device_authorization.failed_limit');
    }

    /**
     * Admin has many morph fields of password history
     *
     * @return MorphMany
     */
    // public function passwordHistories()
    // {
    //   return $this->morphMany(PasswordHistory::class, 'authable');
    // }
    // public function leadingDepartments()
    // {
    //   return $this->hasMany(CompanyDepartment::class, 'head_id', 'id');
    // }

  protected function authorizedToDisapprove(\Approval\Models\Modification $mod): bool
  {
    return $this->authorizedToApproveOrDisapprove($mod);
  }

  /**
   * The programs which are accessible by the admin, It can be program or child program
   */
  public function accessiblePrograms()
  {
    return $this->morphedByMany(Program::class, 'accessable', 'admin_access_lists', 'admin_id', 'accessable_id')->withTimestamps();
  }

  public function emailAccounts()
  {
    return $this->belongsToMany(EmailAccount::class, 'user_email_accounts', 'user_id', 'email_account_id')
      ->withPivot('permission_id');
  }

  // public function hasPermission($permission_name, $account)
  // {
  //   $permission=Permission::where('name',$permission_name)->first();
  // $hasPermission= $this->emailAccounts->contains(function ($emailAccount) use ($permission, $account) {
  //     return $emailAccount->pivot->permission_id == $permission->id && $emailAccount->id == $account->id;
  // });
  // if($hasPermission || $account->created_by==$this->id){
  //   return true;
  // }
  // return false;
  // }

  public function hasPermission(array $permission_names, $account)
  {
    $permissions = Permission::whereIn('name', $permission_names)->get();
    $hasPermission = $this->emailAccounts->contains(function ($emailAccount) use ($permissions, $account) {
      return $permissions->pluck('id')->contains($emailAccount->pivot->permission_id) && $emailAccount->id == $account->id;
    });
    if ($hasPermission || $account->created_by == $this->id) {
      return true;
    }
    return false;
  }

  public function getPermission($account){
    if($account->created_by==$this->id){
      return 'Owner';
    }
    else{
      $permission = DB::table('permissions')
    ->join('user_email_accounts', 'user_email_accounts.permission_id', '=', 'permissions.id')
    ->join('admins', 'admins.id', '=', 'user_email_accounts.user_id')
    ->join('email_accounts', 'email_accounts.id', '=', 'user_email_accounts.email_account_id')
    ->where('admins.id',$this->id)
    ->select('permissions.name')
    ->first();
      return $permission->name;
    }
  }

  public function designation()
  {
    return $this->belongsTo(CompanyDesignation::class, 'designation_id', 'id');
  }

  /**
   * Admin has many morph fields of password history
   *
   * @return MorphMany
   */
  public function passwordHistories()
  {
    return $this->morphMany(PasswordHistory::class, 'authable');
  }
  public function leadingDepartments()
  {
    return $this->hasMany(CompanyDepartment::class, 'head_id', 'id');
  }

  public function fileLogs()
  {
    return $this->morphMany(RFPFileLog::class, 'actioner', 'actioner_type', 'actioner_id');
  }

  public function logs()
  {
    return $this->morphMany(TimelineLog::class, 'actioner', 'actioner_type', 'actioner_id');
  }

  public function personalNotes()
  {
    return $this->morphMany(PersonalNote::class, 'user');
  }

  public function noteTags()
  {
    return $this->morphMany(NoteTag::class, 'user');
  }

  /**
   * Admin has many shared files
   */

  public function sharedFiles()
  {
    return $this->hasMany(FileShare::class, 'user_id', 'id');
  }

  public function sharedByFiles()
  {
    return $this->hasMany(FileShare::class, 'shared_by', 'id');
  }

  public function approvalLevels()
  {
    return $this->belongsToMany(ApprovalLevel::class, ApprovalLevelApprover::class, 'user_id', 'workflow_level_id')->withTimestamps();
  }

  public function approvalLevelIds()
  {
    return $this->approvalLevels->pluck('id')->toArray();
  }

  public function approvalLevelNo()
  {
    return auth()->user()->approvalLevels && auth()->user()->approvalLevels[0] ? ApprovalLevel::pluck('id')->search(auth()->user()->approvalLevels[0]->id) + 1 : 0;
  }

  public function approvalLevelsOrdered(): array
  {
    $levels = [];
    if (auth()->user()->approvalLevelIds()) {
      foreach (auth()->user()->approvalLevelIds() as $id) {
        $levels[] = ApprovalLevel::pluck('id')->search($id) + 1;
      }
    }
    return $levels;
  }

  public function projects()
  {
    return $this->belongsToMany(Project::class, ProjectMember::class, 'admin_id', 'project_id')->withTimestamps();
  }

  public function email_update_logs()
  {
    return $this->morphMany(EmailUpdateLog::class, 'user');
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

    public function projectTemplates()
    {
      return $this->hasMany(ProjectTemplate::class);
    }

    public function contractEvents(): HasMany
    {
      return $this->hasMany(ContractEvent::class);
    }

    public function contractNotifiableUser(): BelongsToMany
    {
      return $this->belongsToMany(Contract::class, 'contract_notifiable_users', 'admin_id', 'contract_id');
    }

        /**
     * Get the review status of each stage for a specific contract.
     *
     * @param int $contractId
     * @return array
     */
    public function getReviewStatusForContract($contract_id)
    {
        // Fetch the contract with its stages
        $contract = Contract::find($contract_id);

        if (!$contract) {
            return []; // or throw an exception, depending on your application's needs
        }

        // Use `$this` to refer to the current admin instance
        $adminId = $this->id;

        $stagesReviewStatus = $contract->stages->map(function ($stage) use ($adminId) {
            // Get the status for this stage for the current admin
            $status = $stage->getUserReviewStatusWithLastReviewDate($adminId);

            // Add stage information for reference
            return [
                'stage_name' => $stage->name,
                'status' => $status['status'],
                'last_review_date' => $status['last_review_date'],
            ];
        });

        return $stagesReviewStatus->toArray();
    }

}
