<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationType extends Model
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
   * Applications of this type
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
