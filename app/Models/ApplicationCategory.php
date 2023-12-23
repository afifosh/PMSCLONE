<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationCategory extends Model
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
   * Applications with this category
   */
  public function applications()
  {
    return $this->hasMany(Application::class, 'category_id');
  }

  /**
   * Scope a query from request params
   */
  public function scopeApplyRequestFilters($q)
  {
    //
  }
}
