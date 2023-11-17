<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractParty extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'contract_party_id',
    'contract_party_type',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function party()
  {
    return $this->morphTo('contract_party', 'contract_party_type', 'contract_party_id');
  }
}
