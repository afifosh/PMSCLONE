<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationSubmitter extends Model
{
  use HasFactory;

  protected $fillable = [
    'application_id',
    'submitter_id',
    'submitter_type',
  ];

  public function application()
  {
    return $this->belongsTo(Application::class);
  }

  public function submitter()
  {
    return $this->morphTo();
  }
}
