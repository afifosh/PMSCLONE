<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Termwind\Components\Dd;

class CompanyContact extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

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

  public function updateIfDirty($attributes)
  {
    $this->fill($attributes);
    if ($this->isDirty()) {
      return $this->save();
    }
  }
}
