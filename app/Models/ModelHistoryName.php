<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHistoryName extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'model_type',
    'model_id',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function model()
  {
    return $this->morphTo();
  }
}
