<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationPipelineStage extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'application_pipeline_id',
    'order',
    'is_default'
  ];

  protected $casts = [
    'created_at' => 'datetime: M d, Y',
    'updated_at' => 'datetime: M d, Y'
  ];

  /**
   * Pipeline that the stage belongs to
   */
  public function pipeline()
  {
    return $this->belongsTo(ApplicationPipeline::class);
  }

  /**
   * Applications in the stage
   */
  public function applications()
  {
    return $this->hasMany(Application::class);
  }
}
