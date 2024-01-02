<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;
use Illuminate\Support\Facades\Storage;

use Approval\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

  // protected $fillable = ['name', 'email', 'status', 'added_by', 'website'];
  protected $fillable = ['name', 'name_ar', 'email', 'status', 'added_by', 'website', 'type', 'address', 'city_id', 'state_id', 'zip', 'country_id', 'phone', 'vat_number', 'gst_number'];



  protected $casts = [
    'verified_at' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  protected $appends = ['avatar'];

  /**
   * updating event to update model history name
   */
  protected static function booted()
  {
    static::updating(function ($model) {
      if (request()->route()->getName() != 'admin.companies.update') {
        // create record in model history name
        if (($model->isDirty('name') && $model->getRawOriginal('name')) || ($model->isDirty('name_ar') && $model->getRawOriginal('name_ar'))) {
          // store old name in model history name
          $model->historyNames()->create(['name' => $model->getRawOriginal('name'), 'name_ar' => $model->getRawOriginal('name_ar')]);
        }
      }
    });
  }
  /**
   * prioritize arabic name over english name
   */
  public function getNameAttribute($value)
  {
    return $this->name_ar ? $this->name_ar : $value;
  }

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
    $completed += $this->isMendatoryKycDocsSubmitted() ? 1 : 0;

    return $completed;
  }

  public function getStepApprovedCountAttribute()
  {
    $completed = 0;
    $completed += $this->detail && !$this->detail->has('modifications')->exists() ? 1 : 0;
    $completed += $this->addresses()->exists() && !$this->addresses()->has('modifications')->exists() ? 1 : 0;
    $completed += $this->contacts()->exists() && !$this->contacts()->has('modifications')->exists() ? 1 : 0;
    $completed += $this->bankAccounts()->exists() && !$this->bankAccounts()->has('modifications')->exists() ? 1 : 0;
    $completed += $this->kycDocs()->exists() && !$this->kycDocs()->has('modifications')->exists() ? 1 : 0;

    return $completed;
  }

  public function profileModelsCount()
  {
    $total = 1 //detail
      + $this->contacts()->count() + $this->POCContact()->count()
      + $this->bankAccounts()->count() + $this->POCBankAccount()->count()
      + $this->addresses()->count() + $this->POCAddress()->count()
      + $this->kycDocs()->count() + $this->POCKycDoc()->count();

    return $total;
  }

  public function profileApprovedPercentage($level = '')
  {
    $level = $level ? $level : $this->approval_level;
    $total = $this->profileModelsCount();
    $comp = $this->withCount(
      [
        'bankAccounts as bank_account_count' => function ($q) {
          $q->has('modifications', 0);
        },
        'addresses as address_count' => function ($q) {
          $q->has('modifications', 0);
        },
        'kycDocs as kyc_doc_count' => function ($q) {
          $q->has('modifications', 0);
        },
        'contacts as contact_count' => function ($q) {
          $q->has('modifications', 0);
        },
        'detail as detail_count' => function ($q) {
          $q->has('modifications', 0);
        },
      ]
    )->find($this->id);
    $approved_count = $comp->detail_count
      + $comp->contact_count
      + $comp->bank_account_count
      + $comp->address_count
      + $comp->kyc_doc_count;

    $approved_count += $this->POCmodifications()->has('approvals', '>=', $level)->count();
    return round(($approved_count / $total) * 100, 1);
  }

  public function profileRejectedPercentage($level = '')
  {
    $total = $this->profileModelsCount();
    $rejected_count = $this->POCmodifications()->has('disapprovals')->count();
    return round(($rejected_count / $total) * 100, 1);
  }

  public function profilePendingApprovalPercentage($level = '')
  {
    $level = $level ? $level : $this->approval_level;
    $total = $this->profileModelsCount();
    $pending_count = $this->POCmodifications()->where(function ($q) use ($level) {
      $q->has('approvals', '<', $level)->doesntHave('disapprovals');
    })->count();
    return round(($pending_count / $total) * 100, 1);
  }

  public function isMendatoryKycDocsSubmitted()
  {
    return $this->getSubmittedMendatoryDocsCount() >= count($this->getMendatoryKycDocs());
  }

  public function getSubmittedMendatoryDocsCount()
  {
    $count = 0;
    foreach ($this->getMendatoryKycDocs() as $i => $doc_id) {
      $this->POCKycDoc()->whereJsonContains('modifications->kyc_doc_id->modified', $doc_id)->count() || $this->kycDocs->where('kyc_doc_id', $doc_id)->count() ? $count++ : '';
    }

    return $count;
  }

  public function getMendatoryKycDocs()
  {
    $locality_type = $this->getPOCLocalityType();
    return KycDocument::whereIn('required_from', [3, $locality_type])->where('is_mendatory', 1)->where('status', 1)->pluck('id')->toArray();
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

  public function projects()
  {
    return $this->belongsToMany(Project::class, 'project_companies');
  }

  /**
   * All of the model's history names.
   */
  public function historyNames(): MorphMany
  {
    return $this->morphMany(ModelHistoryName::class, 'model');
  }

  public function POCDetail()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyDetail::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function COMDetail()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->where(function ($q) {
        $q->whereHas('modifiable', function ($q) {
          $q->where('company_id', $this->id);
        })->orWhereJsonContains('modifications->company_id->modified', $this->id);
      });
    })->whereModifiableType(CompanyDetail::class);
  }

  public function POCContact()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyContact::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function COMContact()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->where(function ($q) {
        $q->whereHas('modifiable', function ($q) {
          $q->where('company_id', $this->id);
        })->orWhereJsonContains('modifications->company_id->modified', $this->id);
      });
    })->whereModifiableType(CompanyContact::class);
  }

  public function POCAddress()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyAddress::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function COMAddress()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->where(function ($q) {
        $q->whereHas('modifiable', function ($q) {
          $q->where('company_id', $this->id);
        })->orWhereJsonContains('modifications->company_id->modified', $this->id);
      });
    })->whereModifiableType(CompanyAddress::class);
  }

  public function POCBankAccount()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(CompanyBankAccount::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function COMBankAccount()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->where(function ($q) {
        $q->whereHas('modifiable', function ($q) {
          $q->where('company_id', $this->id);
        })->orWhereJsonContains('modifications->company_id->modified', $this->id);
      });
    })->whereModifiableType(CompanyBankAccount::class);
  }

  public function POCKycDoc()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);
    return $modificationClass::whereModifiableType(UploadedKycDoc::class)->whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function COMKycDoc()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->where(function ($q) {
        $q->whereHas('modifiable', function ($q) {
          $q->where('company_id', $this->id);
        })->orWhereJsonContains('modifications->company_id->modified', $this->id);
      });
    })->whereModifiableType(UploadedKycDoc::class);
  }

  public function POCmodifications()
  {
    $modificationClass = config('approval.models.modification', \Approval\Models\Modification::class);

    return $modificationClass::where(function ($q) {
      $q->whereHas('modifiable', function ($q) {
        $q->where('company_id', $this->id);
      })->orWhereJsonContains('modifications->company_id->modified', $this->id);
    });
    //return $modificationClass::whereJsonContains('modifications->company_id->modified', $this->id);
  }

  public function isApprovalRequiredForCurrentLevel($level = ''): bool
  {
    $level = $level ? $level : $this->approval_level;
    if ($this->POCmodifications()->has('approvals', '<', $level)->doesntHave('disapprovals')->exists()) {
      return true;
    }

    return false;
  }

  public function incApprovalLevelIfRequired($level = '')
  {
    $level = $level ? $level : $this->approval_level;
    if (!$this->isApprovalRequiredForCurrentLevel()) {
      if ($this->approval_level >= ApprovalLevel::count() && !$this->POCmodifications()->has('disapprovals')->exists()) {
        $this->forceFill(['approval_status' => 1, 'approved_at' => now(), 'verified_at' => now()]); // Approved by all
        $this->approvalRequests()->where('status', 0)->update(['status' => 1]);
      } else {
        if ($this->POCmodifications()->has('disapprovals')->exists()) {
          $this->forceFill(['approval_status' => 3, 'approval_level' => 1]); // Rejected by at least one approver so reset approval level to 1 and set status to rejected
          $this->approvalRequests()->where('status', 0)->update(['status' => 3]);
        } else {
          $this->forceFill(['approval_level' => $this->approval_level + 1]); // increment approval level
        }
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

  public function kycDocs()
  {
    return $this->hasMany(UploadedKycDoc::class);
  }

  public function scopeApplyRequestFilters($query)
  {
    $query->when(request()->has('filter_levels') && is_array(request()->filter_levels), function ($q) {
      $q->whereIn('approval_level', request()->filter_levels);
    })
      ->when(request()->except, function ($q) {
        $q->where('id', '!=', request()->except);
      })
      ->when(request()->get('q'), function ($q) {
        $searchTerm = '%' . request()->get('q') . '%';
        $q->where(function ($query) use ($searchTerm) {
          $query->where('name', 'like', $searchTerm)
            ->orWhere('name_ar', 'like', $searchTerm)
            ->orWhereHas('historyNames', function ($q) use ($searchTerm) {
              $q->where('name', 'like', $searchTerm)
                ->orWhere('name_ar', 'like', $searchTerm);
            });
        });
      })
      ->when(request()->has('contracts') || request()->has('hasContract'), function ($q) {
        $q->has('contracts');
      })
      ->when(request()->has('hasinv'), function ($q) {
        $q->has('contracts.invoices');
      })
      ->when(request()->has('haspayments'), function ($q) {
        $q->has('contracts.invoices.payments');
      })
      ->when(request()->has('type'), function ($q) {
        $q->where('type', request()->type);
      })
      ->when(request()->has('except-parties-of-contract') && request()->get('except-parties-of-contract'), function ($q) {
        $q->whereDoesntHave('pivotPartyOfContracts', function ($q) {
          $q->where('contract_id', request()->get('except-parties-of-contract'));
        });
      });
  }

  /**
   * Pivot contract which has this company as party
   */
  public function pivotPartyOfContracts()
  {
    return $this->hasMany(ContractParty::class, 'contract_party_id')->where('contract_party_type', self::class);
  }

  public function canBeSentForApproval()
  {
    return $this->approval_status != 2 // not already sent for approval
      // && $this->POCmodifications()->doesntHave('disapprovals')->exists(); //  has changed something but not rejected
      && ($this->POCAddress()->exists() || $this->addresses()->exists()) // has changed address or approved address
      && ($this->POCDetail()->exists() || $this->detail()->exists()) // has changed detail or approved detail
      && ($this->POCContact()->exists() || $this->contacts()->exists()) // has changed contact or approved contact
      && ($this->POCBankAccount()->exists() || $this->bankAccounts()->exists()) // has changed bank account or approved bank account
      && ($this->POCKycDoc()->exists() || $this->kycDocs()->exists()) && $this->isMendatoryKycDocsSubmitted() // has changed kyc doc or approved kyc doc
      && ($this->POCmodifications()->exists() && !$this->POCmodifications()->has('disapprovals')->exists()); // has changed something
  }

  public function isEditable()  //user can make changes if not sent for approval
  {
    return $this->approval_status != 2;
  }

  public function getPOCLocalityType()
  {
    $locality_type = null;
    if ($this->POCDetail()->where('is_update', 0)->exists()) {
      $locality_type = @$this->POCDetail()->where('is_update', 0)->first()->modifications['locality_type']['modified'];
    }
    if (!$locality_type && $this->POCDetail()->where('is_update', 1)->exists()) {
      $locality_type = @$this->POCDetail()->where('is_update', 1)->first()->modifications['locality_type']['modified'];
    }
    if (!$locality_type && $this->detail && $this->detail->locality_type) {
      $locality_type = $this->detail->locality_type;
    }

    return $locality_type;
  }

  public function getPOCLogo(): ?string
  {
    $logo = null;
    if ($this->POCDetail()->where('is_update', 0)->exists()) {
      $logo = @$this->POCDetail()->where('is_update', 0)->first()->modifications['logo']['modified'];
    }
    if (!$logo && $this->POCDetail()->where('is_update', 1)->exists()) {
      $logo = @$this->POCDetail()->where('is_update', 1)->first()->modifications['logo']['modified'];
    }
    if (!$logo && $this->detail && $this->detail->logo) {
      $logo = $this->detail->logo;
    }

    return $logo;
  }

  public function getPOCLogoUrl(): ?string
  {
    $logo = $this->getPOCLogo();
    if ($logo && Storage::disk('public')->exists($logo))
      $logo = Storage::disk('public')->url($logo);
    $logo = $logo ? $logo : $this->avatar;

    return $logo;
  }

  public function isHavingPendingProfile()
  {
    return $this->approved_at == null && $this->approval_level == 0;
  }

  public function getDetailsStatus($level = '')
  {
    $status = 'pending';
    if ($this->COMDetail()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->detail && !$this->COMDetail()->exists())
        $status = 'approved';
    } else {
      if ($this->COMDetail()->has('approvals', '>=', $level)->exists() || $this->COMDetail()->count() == 0)
        $status = 'approved';
      if ($this->COMDetail()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getAddressesStatus($level = '')
  {
    $status = 'pending';
    if ($this->COMAddress()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->addresses->count() && !$this->COMAddress()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->COMAddress()->has('approvals', '>=', $level)->count() >= $this->COMAddress()->count())
        $status = 'approved';
      if ($this->COMAddress()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getContactsStatus($level = '')
  {
    $status = 'pending';
    if ($this->COMContact()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->contacts->count() && !$this->COMContact()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->COMContact()->has('approvals', '>=', $level)->count() >= $this->COMContact()->count())
        $status = 'approved';
      if ($this->COMContact()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getBankAccountsStatus($level = '')
  {
    $status = 'pending';
    if ($this->COMBankAccount()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->bankAccounts->count() && !$this->COMBankAccount()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->COMBankAccount()->has('approvals', '>=', $level)->count() >= $this->COMBankAccount()->count())
        $status = 'approved';
      if ($this->COMBankAccount()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getKycDocsStatus($level = '')
  {
    $status = 'pending';
    if ($this->COMKycDoc()->has('disapprovals')->exists())
      $status = 'rejected';
    elseif (!$level) {
      if ($this->kycDocs->count() && !$this->COMKycDoc()->where('active', 1)->exists())
        $status = 'approved';
    } else {
      if ($this->COMKycDoc()->has('approvals', '>=', $level)->count() >= $this->COMKycDoc()->count())
        $status = 'approved';
      if ($this->COMKycDoc()->has('approvals', '<', $level)->exists())
        $status = 'pending';
    }

    return $status;
  }

  public function getOverallStatus($level)
  {
    $status = 0;
    if ($this->getDetailsStatus($level) != 'pending')
      $status += 1;
    if ($this->getAddressesStatus($level) != 'pending')
      $status += 1;
    if ($this->getContactsStatus($level) != 'pending')
      $status += 1;
    if ($this->getBankAccountsStatus($level) != 'pending')
      $status += 1;
    if ($this->getKycDocsStatus($level) != 'pending')
      $status += 1;

    return $status;
  }

  public function profileActivityTimeline($approval_request = '')
  {
    $query = $this->POCmodifications()
      ->with('approvals.approver', 'disapprovals.disapprover', 'modifiable')
      ->withTrashed();
    if ($approval_request) {
      $modificationIds = $this->approvalRequests()->findOrFail($approval_request)->getModificationIds();
      $query->whereIn('id', $modificationIds);
    }

    return $query;
  }

  public function approvalRequests()
  {
    return $this->hasMany(CompanyApprovalRequest::class, 'company_id');
  }

  public function contracts(): MorphMany
  {
    return $this->morphMany(Contract::class, 'assignable');
  }

  /**
   * Get all the location owned by this company. (Locations for placing artworks)
   */
  public function artworkLocations()
  {
    return $this->morphMany(Location::class, 'owner', 'owner_type', 'owner_id');
  }

  public function warehouses()
  {
    return $this->morphMany(Warehouse::class, 'owner');
  }
}
