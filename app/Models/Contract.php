<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'type_id',
    'company_id',
    'project_id',
    'subject',
    'value',
    'start_date',
    'end_date',
    'description',
    'status',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo(ContractType::class);
  }

  public function company(): BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class);
  }

  public function phases(): HasMany
  {
    return $this->hasMany(ContractPhase::class)->orderBy('order');
  }
}
