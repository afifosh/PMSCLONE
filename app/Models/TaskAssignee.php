<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{
  use HasFactory;

  protected $fillable = [
    'task_id',
    'admin_id',
  ];

  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
