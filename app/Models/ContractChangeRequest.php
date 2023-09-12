<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Money;

class ContractChangeRequest extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'reviewed_by',
    'sender_type',
    'sender_id',
    'visible_to_client',
    'reason',
    'description',
    'old_value',
    'new_value',
    'old_currency',
    'new_currency',
    'old_end_date',
    'new_end_date',
    'status',
    'reviewed_at',
  ];

  protected $casts = [
    'old_end_date' => 'datetime:d M, Y',
    'new_end_date' => 'datetime:d M, Y',
    'reviewed_at' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function reviewedBy()
  {
    return $this->belongsTo(Admin::class, 'reviewed_by');
  }

  public function sender()
  {
    return $this->morphTo();
  }

  public function getOldValueAttribute($value)
  {
    return (int) $value / 100;
  }

  public function setOldValueAttribute($value)
  {
    return $this->attributes['old_value'] = Money::{$this->old_currency ?? 'USD'}($value)->getAmount() * 100;
  }

  public function getNewValueAttribute($value)
  {
    return (int) $value / 100;
  }

  public function setNewValueAttribute($value)
  {
    return $this->attributes['new_value'] = Money::{$this->new_currency ?? 'USD'}($value)->getAmount() * 100;
  }

  public function pritableOldValue()
  {
    return Money::{$this->old_currency ?? 'USD'}($this->old_value, true)->format();
  }

  public function pritableNewValue()
  {
    return Money::{$this->new_currency ?? 'USD'}($this->new_value, true)->format();
  }
}
