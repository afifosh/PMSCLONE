<?php

namespace App\Models;

use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
  use HasFactory, SoftDeletes, HasEnum;

  protected $fillable = [
    'type_id',
    'company_id',
    'project_id',
    'subject',
    'value',
    'start_date',
    'end_date',
    'description',
    'status'
  ];

  protected $appends = ['status'];

  public const STATUSES = [
    'Not started',
    'Active',
    'About To Expire',
    'Expired',
    'Terminated',
    'Paused',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getStatusAttribute()
  {
    $value = $this->getRawOriginal('status');
    if($value == 'Terminated' || $value == 'Paused') return $value;
    elseif(!$this->end_date || !$this->start_date) return '';
    elseif($this->end_date->isPast()) return 'Expired';
    elseif($this->start_date->isFuture()) return 'Not started';
    elseif(now() > $this->end_date->subWeeks(2)) return 'About To Expire';
    elseif(now() >= $this->start_date) return 'Active';
  }

  public function getPossibleStatuses()
  {
    $status = $this->getRawOriginal('status');
    if($status == 'Active'){
      return ['Active', 'Paused', 'Terminated'];
    }
    elseif($status == 'Paused'){
      return ['Paused', 'Resumed', 'Terminated'];
    }
    elseif($status == 'Terminated'){
      return ['Resumed', 'Terminated'];
    }
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

  public function events(): HasMany
  {
    return $this->hasMany(ContractEvent::class);
  }

  public function getLatestTerminationReason()
  {
    return $this->events()->where('event_type', 'Terminated')->latest()->first()->modifications['termination_reason'] ?? '';
  }

  public function saveEventLog(ContractUpdateRequest $request, Contract $contract)
  {
    if($contract->start_date->ne($request->start_date) || $contract->end_date->ne($request->end_date)){
      $modifications = ['old' => [], 'new' => []];
      $description = 'Contract Rescheduled From ';
      if($contract->start_date->ne($request->start_date)){
        $modifications['old']['start_date'] = $contract->start_date;
        $modifications['new']['start_date'] = $request->start_date;
        $description .= 'Start Date:  ' . $contract->start_date->format('d M, Y') . ' to ' . $request->start_date;
      }
      if($contract->end_date->ne($request->end_date)){
        $modifications['old']['end_date'] = $contract->end_date;
        $modifications['new']['end_date'] = $request->end_date;
        $description .= 'End Date:  ' . $contract->end_date->format('d M, Y') . ' to ' . $request->end_date;
      }

      $contract->events()->create([
        'event_type' => 'Rescheduled',
        'modifications' => $modifications,
        'description' => $description,
        'admin_id' => auth()->id(),
      ]);
    }

    if($request->status != $contract->getRawOriginal('status')){
      $contract->events()->create([
        'event_type' => $request->status,
        'modifications' => [
          'old' => ['status' => $contract->getRawOriginal('status')],
          'new' => ['status' => $request->status],
        ],
        'description' => 'Contract ' . $request->status,
        'admin_id' => auth()->id(),
      ]);
    }

    if($request->status == 'Terminated' && $request->termination_reason){
      $event = $contract->events()->where('event_type', 'Terminated')->latest()->first();
      $event->modifications = array_merge($event->modifications, ['termination_reason' => $request->termination_reason]);
      $event->save();
    }

    if($request->value != $contract->value){
      $contract->events()->create([
        'event_type' => 'Amount Updated',
        'modifications' => [
          'old' => ['value' => $contract->value],
          'new' => ['value' => $request->value],
        ],
        'description' => 'Contract Amount Updated From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }
  }
}
