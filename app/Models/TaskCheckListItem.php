<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCheckListItem extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'task_id',
    'created_by',
    'completed_by',
    'title',
    'status',
    'order'
  ];

  protected $casts = [
    'status' => 'boolean',
  ];

  public function task()
  {
    return $this->belongsTo(Task::class, 'task_id', 'id');
  }

  public function createdBy()
  {
    return $this->belongsTo(Admin::class, 'created_by', 'id');
  }

  public function completedBy()
  {
    return $this->belongsTo(Admin::class, 'completed_by', 'id');
  }
}
