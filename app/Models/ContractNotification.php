<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractNotification extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'sent_to',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function sentTo()
  {
    return $this->belongsTo(Admin::class, 'sent_to');
  }
}
