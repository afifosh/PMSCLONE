<?php

namespace App\Models;

use App\Innoclapps\Concerns\HasMeta;
use App\Innoclapps\Contracts\Metable;
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
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasPermissions;

class Admin extends Authenticatable implements MustVerifyEmail,  Metable,Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, Impersonate, HasEnum, AuthenticationLoggable, AuthLogs,HasPermissions;
    use \OwenIt\Auditing\Auditable;
    use HasMeta,ApprovesChanges;
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
        'password',
        'status',
        'designation_id',
        'password_changed_at',
        'email_verified_at'
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

    /**
     * Get admin guard key
     *
     * @return string
     */
    public static function GET_LOCK_KEY() {
      return 'VUEXY_ADMIN_LOCK_KEY';
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function getAvatarAttribute($value)
    {
      if(!$value)
      return Avatar::create($this->full_name)->toBase64();
      return @Storage::url($value);
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

    public function authorizedToApproveOrDisapprove(\Approval\Models\Modification $mod) : bool
    {
      if(isset($mod->modifications['company_id']['modified'])){
        $company_id = $mod->modifications['company_id']['modified'];
      }else{
        $company_id = $mod->modifiable->company_id;
      }
      $company = Company::find($company_id);
      $level = ApprovalLevel::pluck('id')->toArray()[$company->approval_level - 1]; // get the current level id
      $approver = ApprovalLevelApprover::where('workflow_level_id', $level)->where('user_id', auth()->id())->first();

      return $approver ? true : false;
    }

    protected function authorizedToApprove(\Approval\Models\Modification $mod) : bool
    {
      return $this->authorizedToApproveOrDisapprove($mod);
    }

    protected function authorizedToDisapprove(\Approval\Models\Modification $mod) : bool
    {
      return $this->authorizedToApproveOrDisapprove($mod);
    }

    public function programs()
    {
      return $this->belongsToMany(Program::class, ProgramUser::class, 'admin_id', 'program_id')->withTimestamps();
    }

    public function emailAccounts()
    {
        return $this->belongsToMany(EmailAccount::class, 'user_email_accounts','user_id','email_account_id');
    }

    public function hasDirectPermission($permission, $account)
    {
    return $this->emailAccounts->contains(function ($emailAccount) use ($permission, $account) {
        return $emailAccount->pivot->permission_id == $permission->id && $emailAccount->id == $account->email_account_id;
    });
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

    public function approvalLevelsOrdered() : array
    {
      $levels = [];
      if(auth()->user()->approvalLevelIds()){
        foreach(auth()->user()->approvalLevelIds() as $id){
          $levels[] = ApprovalLevel::pluck('id')->search($id) + 1;
        }
      }
      return $levels;
    }
}
