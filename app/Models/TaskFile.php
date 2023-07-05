<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
  use HasFactory;

  protected $fillable = [
    'task_id',
    'file_name',
    'file_path',
    'file_extension',
    'file_mime_type',
  ];

  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
