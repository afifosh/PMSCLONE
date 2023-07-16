<?php

namespace App\Models;

use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  use HasFactory;
  use HasLogs;

  protected $fillable = ['program_id', 'category_id', 'name', 'description', 'tags', 'start_date', 'deadline', 'status'];

  protected $casts = [
    'tags' => 'array',
    'start_date' => 'datetime:d M, Y',
    'deadline' => 'datetime:d M, Y',
  ];

  public function scopeMine($query){
    if(auth('admin')->check() && auth('admin')->id() == 1){
      return $query;
    }
    return $query->whereHas('members', function($q){
      return $q->where('admins.id', auth()->id());
    });
  }

  public function isMine(){
    if(auth('admin')->check() && auth('admin')->id() == 1){
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
    return $this->hasMany(Task::class);
  }

  public function progress_percentage()
  {
    $total_tasks = $this->tasks()->count();
    if ($total_tasks == 0) {
      return 0;
    }
    return round(($this->tasks()->where('status', 'Completed')->count() / $total_tasks) * 100, 1);
  }
}
