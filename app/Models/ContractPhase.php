<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPhase extends Model
{
  use HasFactory;

  protected $fillable = [
    'contract_id',
    'name',
    'estimated_cost',
    'start_date',
    'due_date',
    'description',
    'order'
  ];

  protected $appends = ['status'];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public const STATUSES = [
    'Not started',
    'Active',
    'About To Expire',
    'Expired',
  ];

  public const STATUSCOLORS = [
    'Not started' => 'warning',
    'Active' => 'success',
    'About To Expire' => 'warning',
    'Expired' => 'danger',
  ];

  public function getStatusAttribute()
  {
    if($this->due_date->isPast()) return 'Expired';
    elseif($this->start_date->isFuture()) return 'Not started';
    elseif(now() > $this->due_date->subWeeks(2)) return 'About To Expire';
    elseif(now() >= $this->start_date) return 'Active';
  }

  public function contract(): BelongsTo
  {
    return $this->belongsTo(Contract::class);
  }
}
