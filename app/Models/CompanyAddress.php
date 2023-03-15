<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

  protected $fillable = [
    'name',
    'country_id',
    'address_line_1',
    'address_line_2',
    'address_line_3',
    'website',
    'city',
    'state',
    'province',
    'postal_code',
    'zip',
    'phone',
    'fax',
    'email',
    'latitude',
    'longitude',
    'address_type',
    'status',
  ];

  protected $casts = [
    'address_type' => 'array',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
