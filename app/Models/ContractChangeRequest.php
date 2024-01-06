<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Money;

class ContractChangeRequest extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'reviewed_by',
    'requested_at',
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
    'type',
    'data'
  ];

  protected $casts = [
    'data' => 'array',
    'old_end_date' => 'datetime:d M, Y',
    'new_end_date' => 'datetime:d M, Y',
    'reviewed_at' => 'datetime:d M, Y',
    'requested_at' => 'datetime:d M, Y',
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
    return $this->attributes['old_value'] = moneyToInt($value);
  }

  public function getNewValueAttribute($value)
  {
    return (int) $value / 100;
  }

  public function setNewValueAttribute($value)
  {
    return $this->attributes['new_value'] = moneyToInt($value);
  }

  public function pritableOldValue()
  {
    return Money::{$this->old_currency ?? config('money.defaults.currency')}($this->old_value, true)->format();
  }

  public function pritableNewValue()
  {
    return Money::{$this->new_currency ?? config('money.defaults.currency')}($this->new_value, true)->format();
  }

  public function scopeApplyRequestFilters($q)
  {
    $q->when(request()->filter_status, function($q){
      $q->where('status', request()->filter_status);
    });
    $q->when(request()->filter_contract, function($q){
      $q->where('contract_id', request()->filter_contract);
    });
  }
}
