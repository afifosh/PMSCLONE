<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;
use Illuminate\Support\Facades\Storage;

use Approval\Traits\RequiresApproval;

class Company extends BaseModel
{
  use HasFactory, HasEnum;
  use RequiresApproval;

  protected function requiresApprovalWhen(array $modifications): bool
  {
    // Handle some logic that determines if this change requires approval
    //
    // Return true if the model requires approval, return false if it
    // should update immediately without approval.
    return false;
  }


  public const DT_ID = 'companies_datatable';

  protected $fillable = ['name', 'email', 'status', 'added_by', 'website'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAvatarAttribute($value)
  {
    if (!$value)
      return @$this->detail->logo ? @Storage::url($this->detail->logo) : Avatar::create($this->name)->toBase64();
    return @Storage::url($value);
  }

  public function getStepCompletedCountAttribute()
  {
    $completed = 0;
    $completed += $this->POCDetail()->exists() || $this->detail ? 1 : 0;
    $completed += $this->POCContact()->exists() || $this->contacts->count() ? 1 : 0;
    $completed += $this->POCAddress()->exists() || $this->addresses->count() ? 1 : 0;
    $completed += $this->POCBankAccount()->exists() || $this->bankAccounts->count() ? 1 : 0;
    return $completed;
  }

  public function addedBy()
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }
  public function users()
  {
    return $this->hasMany(User::class);
  }

  public function contactPersons()
  {
    return $this->hasMany(CompanyContactPerson::class);
  }

  public function detail()
  {
    return $this->hasOne(CompanyDetail::class);
  }

  public function POCDetail()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyDetail::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function POCContact()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyContact::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function POCAddress()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyAddress::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function POCBankAccount()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyBankAccount::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function POCmodifications()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function($q){
      $q->whereHas('modifiable', function($q){
        $q->where('company_id', $this->id);
      })->orWhereJsonContains('modifications->company_id->modified', $this->id);
    });
    //return $modificationClass::whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function isApprovalRequiredForCurrentLevel($level = ''): bool
  {
    $is_inc = true;
    $level = $level ? $level : $this->approval_level;
    if ($this->POCmodifications()->count() > 0) {
      foreach ($this->POCmodifications()->get() as $modification) {
        if (($level > $modification->approvals()->count()) && $modification->disapprovals()->count() == 0) {
          $is_inc = false;
          break;
        }
      }
    }
    return !$is_inc;
  }

  public function incApprovalLevelIfRequired($level = '')
  {
    $level = $level ? $level : $this->approval_level;
    $reject = false;
    if (!$this->isApprovalRequiredForCurrentLevel()) {
      if ($this->approval_level >= ApprovalLevel::count())
        $this->forceFill(['approval_status' => 1, 'approved_at' => now(), 'verified_at' => now()]); // Approved by all
      else {
        if ($this->POCmodifications()->count() > 0) {
          foreach ($this->POCmodifications()->get() as $modification) {
            if ($modification->disapprovals()->count() > 0) {
              $reject = true;
              break;
            }
          }
        }
        if ($reject)
          $this->forceFill(['approval_status' => 3, 'approval_level' => 1]); // Rejected by at least one
        else
          $this->forceFill(['approval_level' => $this->approval_level + 1]); // increment approval level
      }
      $this->save();

      return true;
    }
  }

  public function draftDetail()
  {
    return $this->morphOne(DraftData::class, 'draftable')->where('type', 'detail');
  }

  public function draftData()
  {
    return $this->morphMany(DraftData::class, 'draftable');
  }

  public function addresses()
  {
    return $this->hasMany(CompanyAddress::class);
  }

  public function bankAccounts()
  {
    return $this->hasMany(CompanyBankAccount::class);
  }

  public function contacts()
  {
    return $this->hasMany(CompanyContact::class);
  }

  public function scopeApplyRequestFilters($query)
  {
    $query->when(request()->has('filter_levels') && is_array(request()->filter_levels), function ($q) {
      $q->whereIn('approval_level', request()->filter_levels);
    });
  }

  public function canBeSentForApproval()
  {
    return $this->approval_status != 2 // not already sent for approval
      && ($this->POCAddress()->exists() || $this->addresses->count()) // has changed address or approved address
      && ($this->POCDetail()->exists() || $this->detail) // has changed detail or approved detail
      && ($this->POCContact()->exists() || $this->contacts->count()) // has changed contact or approved contact
      && ($this->POCBankAccount()->exists() || $this->bankAccounts->count()) // has changed bank account or approved bank account
      && $this->POCmodifications()->count(); // has changed something
  }

  public function isEditable()  //user can make changes if not sent for approval
  {
    return $this->approval_status != 2;
  }

  public function getPOCLocalityType()
  {
    $locality_type = null;
    if (auth()->user()->company->POCDetail()->where('is_update', 0)->exists()) {
      $locality_type = @auth()->user()->company->POCDetail()->where('is_update', 0)->first()->modifications['locality_type']['modified'];
    }
    if (!$locality_type && auth()->user()->company->POCDetail()->where('is_update', 1)->exists()) {
      $locality_type = @auth()->user()->company->POCDetail()->where('is_update', 1)->first()->modifications['locality_type']['modified'];
    }
    if (!$locality_type && auth()->user()->company->detail && auth()->user()->company->detail->locality_type) {
      $locality_type = auth()->user()->company->detail->locality_type;
    }

    return $locality_type;
  }

  public function getPOCLogo(): ?string
  {
    $logo = null;
    if (auth()->user()->company->POCDetail()->where('is_update', 0)->exists()) {
      $logo = @auth()->user()->company->POCDetail()->where('is_update', 0)->first()->modifications['logo']['modified'];
    }
    if (!$logo && auth()->user()->company->POCDetail()->where('is_update', 1)->exists()) {
      $logo = @auth()->user()->company->POCDetail()->where('is_update', 1)->first()->modifications['logo']['modified'];
    }
    if (!$logo && auth()->user()->company->detail && auth()->user()->company->detail->logo) {
      $logo = auth()->user()->company->detail->logo;
    }

    return $logo;
  }

  public function getPOCLogoUrl(): ?string
  {
    $logo = $this->getPOCLogo();
    if ($logo && Storage::disk('public')->exists($logo))
      $logo = Storage::disk('public')->url($logo);

    $logo = $logo ? $logo : auth()->user()->company->avatar;

    return $logo;
  }

  public function isHavingPendingProfile()
  {
    return $this->approved_at == null && $this->approval_level == 0;
  }

  public function getDetailsStatus($level = '')
  {
    $status = 'pending';
    if ($this->POCDetail()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->detail && !$this->POCDetail()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->POCDetail()->has('approvals', '>=', $level)->exists())
        $status = 'approved';
      if ($this->POCDetail()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getAddressesStatus($level = '')
  {
    $status = 'pending';
    if ($this->POCAddress()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->addresses->count() && !$this->POCAddress()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->POCAddress()->has('approvals', '>=', $level)->count() >= $this->POCAddress()->count())
        $status = 'approved';
      if ($this->POCAddress()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getContactsStatus($level = '')
  {
    $status = 'pending';
    if ($this->POCContact()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->contacts->count() && !$this->POCContact()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->POCContact()->has('approvals', '>=', $level)->count() >= $this->POCContact()->count())
        $status = 'approved';
      if ($this->POCContact()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getBankAccountsStatus($level = '')
  {
    $status = 'pending';
    if ($this->POCBankAccount()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->bankAccounts->count() && !$this->POCBankAccount()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->POCBankAccount()->has('approvals', '>=', $level)->count() >= $this->POCBankAccount()->count())
        $status = 'approved';
      if ($this->POCBankAccount()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }
}
