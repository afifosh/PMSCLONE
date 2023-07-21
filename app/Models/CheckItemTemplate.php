<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckItemTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'task_template_id',
    'created_by',
    'title',
    'status',
    'order',
  ];

  public function taskTemplate()
  {
    return $this->belongsTo(TaskTemplate::class);
  }

  public function createdBy()
  {
    return $this->belongsTo(Admin::class);
  }
}
