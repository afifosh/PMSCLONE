<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

  protected $fillable = [
    'country_id',
    'name',
    'branch',
    'street',
    'city',
    'state',
    'post_code',
    'account_no',
    'iban_no',
    'swift_code'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function updateIfDirty($attributes)
  {
    $this->fill($attributes);
    if ($this->isDirty()) {
      return $this->save();
    }
  }

  public static function getFields()
  {
    return [
      'Country' => 'country_id',
      'Name' => 'name',
      'Branch' => 'branch',
      'Street' => 'street',
      'City' => 'city',
      'State' => 'state',
      'Post Code' => 'post_code',
      'Account No' => 'account_no',
      'IBAN No' => 'iban_no',
      'Swift Code' => 'swift_code'
    ];
  }
}
