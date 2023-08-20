<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'type_id',
    'company_id',
    'project_id',
    'subject',
    'value',
    'start_date',
    'end_date',
    'description'
  ];

  public const STATUSES = [
    'Not started',
    'Active',
    'About To Expire',
    'Expired',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getStatusAttribute()
  {
    if($this->end_date->isPast()) return 'Expired';
    elseif($this->start_date->isFuture()) return 'Not started';
    elseif(now() > $this->end_date->subWeeks(2)) return 'About To Expire';
    elseif(now() >= $this->start_date) return 'Active';
  }

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
