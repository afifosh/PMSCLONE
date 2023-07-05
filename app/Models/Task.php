<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'subject',
    'description',
    'status',
    'priority',
    'start_date',
    'due_date',
    'tags',
    'admin_id',
  ];

  protected $casts = [
    'tags' => 'array',
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
  ];

  public function files()
  {
    return $this->hasMany(TaskFile::class);
  }

  public function followers()
  {
    return $this->belongsToMany(Admin::class, TaskFollower::class, 'task_id', 'admin_id')->withTimestamps();
  }

  public function assignees()
  {
    return $this->belongsToMany(Admin::class, TaskAssignee::class, 'task_id', 'admin_id')->withTimestamps();
  }
}
