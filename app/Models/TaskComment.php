<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
  use HasFactory;

  protected $fillable = ['comment', 'admin_id'];

  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
