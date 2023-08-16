<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPhase extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'contract_id',
    'name',
    'estimated_cost',
    'start_date',
    'due_date',
    'description',
    'status',
    'order'
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contract(): BelongsTo
  {
    return $this->belongsTo(Contract::class);
  }
}
