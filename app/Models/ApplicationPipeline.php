<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationPipeline extends Model
{
  use HasFactory;

  protected $fillable = [
    'name'
  ];

  protected $casts = [
    'created_at' => 'datetime: M d, Y',
    'updated_at' => 'datetime: M d, Y'
  ];

  /**
   * Stage of the pipeline
   */
  public function stages()
  {
    return $this->hasMany(ApplicationPipelineStage::class)->orderBy('order');
  }

  /**
   * Applications in the pipeline
   */
  public function applications()
  {
    return $this->hasMany(Application::class, 'pipeline_id');
  }

  /**
   * Scope a query from request params
   */
  public function scopeApplyRequestFilters($q)
  {
    //
  }
}
