<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteTag extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'color',
  ];

  public function notes()
  {
    return $this->hasMany(PersonalNote::class);
  }

  public function user()
  {
    return $this->morphTo();
  }
}
