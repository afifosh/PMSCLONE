<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractEvent extends Model
{
  use HasFactory;

  protected $fillable = [
    'modifications',
    'event_type',
    'description',
    'admin_id'
  ];

  protected $casts = [
    'modifications' => 'array',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }
}
