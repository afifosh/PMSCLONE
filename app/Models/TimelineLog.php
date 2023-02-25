<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineLog extends Model
{
  use HasFactory;

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
    'data' => 'json'
  ];

  protected $fillable = ['logable_type', 'logable_id', 'actioner_type', 'actioner_id', 'log', 'data'];

  public function logable()
  {
    return $this->morphTo('logable', 'logable_type', 'logable_id');
  }

  public function actioner()
  {
    return $this->morphTo('actioner', 'actioner_type', 'actioner_id');
  }
}
