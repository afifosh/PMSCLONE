<?php

namespace App\Models;

use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Akaunting\Money\Money;

class Contract extends Model
{
  use HasFactory, SoftDeletes, HasEnum;

  protected $fillable = [
    'type_id',
    'project_id',
    'assignable_type',
    'assignable_id',
    'program_id',
    'refrence_id',
    'subject',
    'currency',
    'value',
    'start_date',
    'end_date',
    'description',
    'status'
  ];

  protected $appends = ['status', 'printable_value'];

  public const STATUSES = [
    'Not started',
    'Active',
    'About To Expire',
    'Expired',
    'Draft',
    'Terminated',
    'Paused',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getValueAttribute($value)
  {
    return $value / 100;
  }

  public function setValueAttribute($value)
  {
    return $this->attributes['value'] = Money::{$this->currency ?? 'USD'}($value)->getAmount() * 100;
  }

  public function getStatusAttribute()
  {
    $value = $this->getRawOriginal('status');
    if ($value == 'Terminated' || $value == 'Paused' || $value == 'Draft') return $value;
    elseif (!$this->end_date || !$this->start_date) return '';
    elseif ($this->end_date->isPast()) return 'Expired';
    elseif ($this->start_date->isFuture()) return 'Not started';
    elseif (now() > $this->end_date->subWeeks(2)) return 'About To Expire';
    elseif (now() >= $this->start_date) return 'Active';
  }

  public function getPossibleStatuses()
  {
    $status = $this->getRawOriginal('status');
    if ($status == 'Active') {
      return ['Active', 'Paused', 'Terminated'];
    } elseif ($status == 'Paused') {
      return ['Paused', 'Resumed', 'Terminated'];
    } elseif ($status == 'Terminated') {
      return ['Resumed', 'Terminated'];
    }
  }

  public function scopeApplyRequestFilters($q)
  {
    return $q->when(request()->filter_status, function ($q) {
      if (request()->filter_status == 'Draft') return $q->where('status', 'Draft');
      else if (request()->filter_status == 'Not started') {
        $q->where('start_date', '>', now());
      } elseif (request()->filter_status == 'Expired') {
        $q->where('status', 'Active')->where('end_date', '<', now());
      } elseif (request()->filter_status == 'Terminated') {
        $q->where('status', 'Terminated');
      } elseif (request()->filter_status == 'Paused') {
        $q->where('status', 'Paused');
      } elseif (request()->filter_status == 'Active') {
        $q->where('status', 'Active')->where('start_date', '<=', now())->where('end_date', '>=', now())->where('end_date', '>=', now()->addWeeks(2));
      } elseif (request()->filter_status == 'About To Expire') {
        $q->where('status', 'Active')->where('end_date', '>', now())->where('end_date', '<', now()->addWeeks(2));
      }
    })->when(request()->companies, function ($q) {
      $q->where('assignable_type', Company::class)->where('assignable_id', request()->companies);
    })
      ->when(request()->contract_client, function ($q) {
        $q->where('assignable_type', Client::class)->where('assignable_id', request()->contract_client);
      })
      ->when(request()->search_q, function ($q) {
        $q->where(function ($q) {
          $q->where('subject', 'like', '%' . request()->search_q . '%')
            ->orWhereHas('phases', function ($q) {
              $q->where('name', 'like', '%' . request()->search_q . '%');
            });
        });
      })->when(request()->contract_type, function ($q) {
        $q->whereHas('type', function ($q) {
          $q->where('id', request()->contract_type);
        });
      })->when(request()->projects, function ($q) {
        $q->whereHas('project', function ($q) {
          $q->where('id', request()->projects);
        });
      })->when(request()->programs, function ($q) {
        $q->whereHas('program', function ($q) {
          $q->where('id', request()->programs);
        });
      });
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo(ContractType::class);
  }

  public function assignable()
  {
    return $this->morphTo();
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

  public function expiryNotificaions(): HasMany
  {
    return $this->hasMany(ContractNotification::class);
  }

  public function lastExpiryNotification(): HasOne
  {
    return $this->hasOne(ContractNotification::class)->latest();
  }

  public function notifiableUsers(): BelongsToMany
  {
    return $this->belongsToMany(Admin::class, 'contract_notifiable_users');
  }

  public function getStatusColor()
  {
    $status = $this->status;
    if ($status == 'Active') return 'success';
    elseif ($status == 'Not started') return 'warning';
    elseif ($status == 'About To Expire') return 'warning';
    elseif ($status == 'Expired') return 'danger';
    elseif ($status == 'Terminated') return 'danger';
    elseif ($status == 'Paused') return 'warning';
  }

  public function remaining_cost($phase_to_ignore_id = null)
  {
    if ($phase_to_ignore_id) {
      return $this->value - $this->phases->where('id', '!=', $phase_to_ignore_id)->sum('estimated_cost');
    }
    return $this->value - $this->phases->sum('estimated_cost');
  }

  public function saveEventLog(ContractUpdateRequest $request, Contract $contract)
  {
    $end_date_updated = false;
    if(($contract->end_date && !$request->end_date)
    || (!$contract->end_date && $request->end_date)
    || ($contract->end_date && $request->end_date && $contract->end_date->ne($request->end_date))){
      $end_date_updated = true;
    }

    if ($contract->start_date->ne($request->start_date) || $end_date_updated) {
      $modifications = ['old' => [], 'new' => []];
      $description = 'Contract Rescheduled From ';
      if ($contract->start_date->ne($request->start_date)) {
        $modifications['old']['start_date'] = $contract->start_date;
        $modifications['new']['start_date'] = $request->start_date;
        $description .= 'Start Date:  ' . $contract->start_date->format('d M, Y') . ' to ' . $request->start_date;
      }
      if ($end_date_updated) {
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

    // if($request->status != $contract->getRawOriginal('status')){
    //   $contract->events()->create([
    //     'event_type' => $request->status,
    //     'modifications' => [
    //       'old' => ['status' => $contract->getRawOriginal('status')],
    //       'new' => ['status' => $request->status],
    //     ],
    //     'description' => 'Contract ' . $request->status,
    //     'admin_id' => auth()->id(),
    //   ]);
    // }

    // if($request->status == 'Terminated' && $request->termination_reason){
    //   $event = $contract->events()->where('event_type', 'Terminated')->latest()->first();
    //   $event->modifications = array_merge($event->modifications, ['termination_reason' => $request->termination_reason]);
    //   $event->save();
    // }

    // if($request->value != $contract->value){
    //   $contract->events()->create([
    //     'event_type' => 'Amount Updated',
    //     'modifications' => [
    //       'old' => ['value' => $contract->value],
    //       'new' => ['value' => $request->value],
    //     ],
    //     'description' => 'Contract Amount Updated From ' . $contract->value . ' to ' . $request->value,
    //     'admin_id' => auth()->id(),
    //   ]);
    // }
  }

  public function formatPhaseValue($value)
  {
    return Money::{$this->currency ?? 'USD'}($value)->getAmount();
  }

  public function getPrintableValueAttribute()
  {
    return Money::{$this->currency ?? 'USD'}($this->value, true)->format();
  }

  public function program(): BelongsTo
  {
    return $this->belongsTo(Program::class);
  }
}
