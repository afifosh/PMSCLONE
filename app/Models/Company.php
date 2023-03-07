<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;
// use Approval\Traits\RequiresApproval;

class Company extends BaseModel
{
  use HasFactory, HasEnum;
//   use RequiresApproval;

//   protected function requiresApprovalWhen(array $modifications) : bool
// {
//     // Handle some logic that determines if this change requires approval
//     //
//     // Return true if the model requires approval, return false if it
//     // should update immediately without approval.
//     return false;
// }


  public const DT_ID = 'companies_datatable';

  protected $fillable = ['name', 'email', 'status', 'added_by', 'website'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAvatarAttribute($value)
  {
    if(!$value)
      return Avatar::create($this->name)->toBase64();
    return $value;
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
}
