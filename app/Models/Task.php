<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Media\HasMedia;
use Spatie\Comments\Models\Concerns\HasComments;

class Task extends Model
{
  use HasFactory, HasEnum;
  use HasMedia;
  use HasComments;

  protected $fillable = [
    'subject',
    'description',
    'status',
    'priority',
    'start_date',
    'due_date',
    'tags',
    'admin_id',
    'is_completed_checklist_hidden',
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

  public function reminders()
  {
    return $this->hasMany(TaskReminder::class, 'task_id', 'id');
  }

  // public function comments()
  // {
  //   return $this->hasMany(TaskComment::class, 'task_id', 'id');
  // }

  /*
 * This string will be used in notifications on what a new comment
 * was made.
 */
  public function commentableName(): string
  {
    return 'Task: ' . $this->subject . ' Of ' . $this->project->name ;
  }

  /*
 * This URL will be used in notifications to let the user know
 * where the comment itself can be read.
 */
  public function commentUrl(): string
  {
    return route('admin.projects.tasks.index', ['view' => $this->id, 'tab' => 'comments', 'project' => $this->project_id]);
  }
}
