<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationScoreCard extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'meta'
  ];

  protected $casts = [
    'meta' => 'array',
    'created_at' => 'datetime: M d, Y',
    'updated_at' => 'datetime: M d, Y'
  ];

  /**
   * Applications that use this scorecard
   */
  public function applications()
  {
    return $this->hasMany(Application::class);
  }

  /**
   * Scope a query from request params
   */
  public function scopeApplyRequestFilters($q)
  {
    //
  }
}
