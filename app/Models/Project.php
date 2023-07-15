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
