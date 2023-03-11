<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
  use HasFactory;

  protected $fillable = [
    'company_id',
    'type',
    'title',
    'first_name',
    'last_name',
    'position',
    'phone',
    'mobile',
    'email',
    'fax',
    'poa',
    'status',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
