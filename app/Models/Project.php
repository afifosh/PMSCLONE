<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  use HasFactory;

  protected $fillable = ['program_id', 'category_id', 'name', 'description', 'tags', 'start_date', 'deadline', 'status'];

  protected $casts = [
    'tags' => 'array',
    'start_date' => 'datetime:d M, Y',
    'deadline' => 'datetime:d M, Y',
  ];

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
}
