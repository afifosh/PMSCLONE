<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCheckListItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'task_id',
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
}
