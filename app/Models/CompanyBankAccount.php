<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
  use HasFactory;

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
}
