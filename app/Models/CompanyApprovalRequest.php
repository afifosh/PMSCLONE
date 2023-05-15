<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyApprovalRequest extends Model
{
  use HasFactory, Tenantable;

  protected $fillable = ['sent_by', 'type'];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function sentBy()
  {
    return $this->belongsTo(User::class, 'sent_by');
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function modifications()
  {
    // return $this->belongsToMany(Modification::class, 'approval_request_modifications', 'company_approval_request_id', 'modification_id');
    return $this->belongsToMany(Modification::class, ApprovalRequestModification::class, 'approval_request_id', 'modification_id');
  }

  public function getModificationIds()
  {
    return $this->modifications->pluck('id')->toArray();
  }

  public function modificationsPendingApprovalPercentage()
  {
    $total = $this->modifications()->count();
    $pending_count = $this->modifications()->doesntHave('approvals')->doesntHave('disapprovals')->count();
    if($total == 0)
      return 0;
    return round(($pending_count / $total) * 100, 1);
  }

  public function modificationsApprovedPercentage()
  {
    $total = $this->modifications()->count();
    $approved_count = $this->modifications()->has('approvals' , '>=', ApprovalLevel::count())->count();
    if($total == 0)
      return 0;
    return round(($approved_count / $total) * 100, 1);
  }

  public function modificationsRejectedPercentage()
  {
    $total = $this->modifications()->count();
    $rejected_count = $this->modifications()->has('disapprovals')->count();
    if($total == 0)
      return 0;
    return round(($rejected_count / $total) * 100, 1);
  }
}
