<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
  use HasFactory;

  protected $table = 'approval_workflow_levels';

  protected $fillable = [
    'workflow_id',
    'name'
  ];

  public function workflow()
  {
    return $this->belongsTo(Workflow::class);
  }

  public function approvers()
  {
    return $this->belongsToMany(Admin::class, 'approval_level_approvers', 'workflow_level_id', 'user_id');
  }
}
