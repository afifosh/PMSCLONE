<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Media\HasMedia;

class Task extends Model
{
  use HasFactory, HasEnum;
  use HasMedia;

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

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

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

  public function checklistItems()
  {
    return $this->hasMany(TaskCheckListItem::class, 'task_id', 'id')->orderBy('order');
  }
}
