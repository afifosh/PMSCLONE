<?php

namespace App\Models;

use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Models\Group;
use Modules\Chat\Repositories\GroupRepository;

class Project extends Model
{
  use HasFactory;
  use HasLogs;

  protected $fillable = ['program_id', 'category_id', 'company_id', 'name', 'description', 'is_progress_calculatable', 'tags', 'start_date', 'deadline', 'status', 'budget', 'refrence_id'];

  public const STATUSES = [
    '0' => 'Not Started',
    '1' => 'In Progress',
    '2' => 'On Hold',
    '3' => 'Cancelled',
    '4' => 'Completed',
  ];

  protected $casts = [
    'tags' => 'array',
    'start_date' => 'datetime:d M, Y',
    'deadline' => 'datetime:d M, Y',
  ];

  public function scopeMine($query)
  {
    if (auth('admin')->check() && auth('admin')->id() == 1) {
      return $query;
    }
    return $query->whereHas('members', function ($q) {
      return $q->where('admins.id', auth()->id());
    });
  }

  public function isMine()
  {
    if (auth('admin')->check() && auth('admin')->id() == 1) {
      return true;
    }
    return $this->members->contains(auth('admin')->id());
  }

  public function resolveStatus()
  {
    switch ($this->status) {
      case '0':
        return ['color' => 'warning', 'status' => 'Not Started'];
        break;
      case '1':
        return ['color' => 'success', 'status' => 'In Progress'];
        break;
      case '2':
        return ['color' => 'danger', 'status' => 'On Hold'];
        break;
      case '3':
        return ['color' => 'danger', 'status' => 'Cancelled'];
        break;
      case '4':
        return ['color' => 'success', 'status' => 'Completed'];
        break;
      default:
        return ['color' => 'danger', 'status' => 'Unknown'];
        break;
    }
  }

  public function program()
  {
    return $this->belongsTo(Program::class, 'program_id', 'id');
  }

  public function category()
  {
    return $this->belongsTo(ProjectCategory::class, 'category_id', 'id');
  }

  public function members()
  {
    return $this->belongsToMany(Admin::class, ProjectMember::class, 'project_id', 'admin_id')->withTimestamps();
  }

  public function tasks()
  {
    return $this->hasMany(Task::class)->orderBy('order');
  }

  public function contracts(): HasMany
  {
    return $this->hasMany(Contract::class);
  }

  public function progress_percentage()
  {
    $total_tasks = $this->tasks->count();
    if ($total_tasks == 0) {
      return 0;
    }
    return round(($this->tasks->where('status', 'Completed')->count() / $total_tasks) * 100, 1);
  }

  public static function getProjectsStatusesChartData()
  {
    $projects = Project::mine()->get();
    $data['labels'] = ['Not Started', 'In Progress', 'On Hold', 'Cancelled', 'Completed'];
    $data['datasets'][0]['label'] = 'Projects';
    $data['datasets'][0]['data'] = [];
    $data['datasets'][0]['backgroundColor'] = [
      'rgb(255, 99, 132)',
      'rgb(54, 162, 235)',
      'rgb(255, 205, 86)',
      'rgb(255, 159, 64)',
      'rgb(75, 192, 192)'
    ];
    $data['datasets'][0]['hoverOffset'] = 4;
    $statuses = ['Not Started', 'In Progress', 'On Hold', 'Cancelled', 'Completed'];
    foreach ($statuses as $status) {
      $data['datasets'][0]['data'][] = $projects->where('status', array_search($status, $statuses))->count();
    }

    return $data;
  }

  public function group()
  {
    return $this->hasOne(Group::class, 'project_id', 'id');
  }

  public function sendMessageInChat($message, $toOthers = true, $type = Conversation::MESSAGE_TYPE_BADGES)
  {
    if (!$this->group) {
      return false;
    }
    $msgInput = [
      'to_id' => $this->group->id,
      'message' => $message,
      'is_group' => true,
      'message_type' => $type,
    ];

    $repo = app(GroupRepository::class);
    $repo->sendMessage($msgInput, $toOthers);

    return true;
  }

  public function companies()
  {
    return $this->belongsToMany(Company::class, 'project_companies', 'project_id', 'company_id')->withTimestamps();
  }
}
