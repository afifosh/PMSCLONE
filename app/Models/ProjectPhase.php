<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'project_id',
    'name',
    'description',
    'estimated_cost',
    'status',
    'order',
    'start_date',
    'due_date',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
  ];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
