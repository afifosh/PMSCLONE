<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'project_template_id',
    'subject',
    'description',
    'priority',
    'is_completed_checklist_hidden',
    'tags',
    'status',
    'order',
  ];

  protected $casts = [
    'tags' => 'array',
  ];

  public function projectTemplate()
  {
    return $this->belongsTo(ProjectTemplate::class);
  }

  public function checkItemTemplates()
  {
    return $this->hasMany(CheckItemTemplate::class)->orderBy('order');
  }
}
