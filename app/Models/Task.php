<?php

namespace App\Models;

use App\Traits\HasEnum;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Media\HasMedia;
use Spatie\Comments\Models\Concerns\HasComments;

class Task extends Model
{
  use HasFactory, HasEnum;
  use HasMedia;
  use HasComments;
  use HasLogs;

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
    'order'
  ];

  protected $casts = [
    'tags' => 'array',
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
  ];

  public function scopeApplyRequestFilters($query)
  {
    $query->when(request()->has('filter_projects') && is_array(request()->filter_projects), function ($q) {
      $q->whereIn('project_id', request()->filter_projects);
    });

    $query->when(request()->has('filer_status') && is_array(request()->filer_status), function ($q) {
      $q->whereIn('status', request()->filer_status);
    });
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
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

  /*
 * This string will be used in notifications on what a new comment
 * was made.
 */
  public function commentableName(): string
  {
    return 'Task: ' . $this->subject . ' Of ' . $this->project->name;
  }

  /*
 * This URL will be used in notifications to let the user know
 * where the comment itself can be read.
 */
  public function commentUrl(): string
  {
    return route('admin.projects.tasks.index', ['view' => $this->id, 'tab' => 'comments', 'project' => $this->project_id]);
  }

  public function progress_percentage()
  {
    $total = $this->checklistItems->count();
    if ($total == 0) {
      return 0;
    }

    $completed = $this->checklistItems->whereNotNull('completed_by')->count();

    return round(($completed / $total) * 100, 1);
  }

  public function getPaginatedLogs()
  {
    return TimelineLog::where(function ($q) {
        $q->where('logable_type', Task::class)
          ->where('logable_id', $this->id);
      })
      ->orWhere(function ($q) {
        $q->where('logable_type', TaskCheckListItem::class)
          ->whereIn('logable_id', $this->checklistItems()->pluck('id')->toArray());
      })
      ->orWhere(function ($q) {
        $q->where('logable_type', Media::class)
          ->whereIn('logable_id', $this->media()->pluck('id')->toArray());
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10);
  }
}
