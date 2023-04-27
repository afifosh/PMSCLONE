<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyApprovalRequest extends Model
{
  use HasFactory;

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function modifications()
  {
    // return $this->belongsToMany(Modification::class, 'approval_request_modifications', 'company_approval_request_id', 'modification_id');
    return $this->belongsToMany(Modification::class, ApprovalRequestModification::class, 'approval_request_id', 'modification_id');
  }
}
