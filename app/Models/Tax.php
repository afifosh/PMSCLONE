<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'type',
    'amount',
    'status',
    'is_retention'
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getAmountAttribute($value)
  {
    return $value / 1000;
  }

  public function setAmountAttribute($value)
  {
    $this->attributes['amount'] = $value * 1000;
  }
}
