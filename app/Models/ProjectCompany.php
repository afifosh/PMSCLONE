<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCompany extends Model
{
  use HasFactory;

  protected $fillable = [
    'project_id',
    'company_id',
  ];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
