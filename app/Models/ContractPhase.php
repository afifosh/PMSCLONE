<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPhase extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function milestones()
  {
    return $this->hasMany(ContractMilestone::class, 'phase_id');
  }
}
