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
    return $modificationClass::whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function isApprovalRequiredForCurrentLevel($level = ''): bool
  {
    $is_inc = true;
    $level = $level ? $level : $this->approval_level;
    if ($this->POCmodifications()->count() > 0) {
      foreach ($this->POCmodifications()->get() as $modification) {
        if ($level > $modification->approvals()->count()) {
          $is_inc = false;
          break;
        }
      }
    }
    return !$is_inc;
  }

  public function incApprovalLevelIfRequired()
  {
    if (!$this->isApprovalRequiredForCurrentLevel()) {
      if ($this->approval_level >= ApprovalLevel::count())
        $this->forceFill(['approval_status' => 1]); // Approved by all
      else
        $this->forceFill(['approval_level' => $this->approval_level + 1]);
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
}
