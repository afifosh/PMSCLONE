<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'admin_id',
    'name',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d',
    'updated_at' => 'datetime:Y-m-d'
  ];

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }

  public function taskTemplates()
  {
    return $this->hasMany(TaskTemplate::class)->orderBy('order');
  }
}
