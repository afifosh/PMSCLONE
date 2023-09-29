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
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Contract extends Model
{
  use HasFactory, SoftDeletes, HasEnum;

  protected $fillable = [
    'category_id',
    'type_id',
    'project_id',
    'assignable_type',
    'assignable_id',
    'program_id',
    'signature_date',
    'account_balance_id',
    'remaining_amount',
    'invoice_method',
    'refrence_id',
    'subject',
    'currency',
    'value',
    'start_date',
    'end_date',
    'description',
    'visible_to_client',
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
    'signature_date' => 'datetime:d M, Y',
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
    return $this->attributes['value'] = Money::{$this->currency ?? config('money.defaults.currency')}($value)->getAmount() * 100;
  }

  public function getRemainingAmountAttribute($value)
  {
    return $value / 100;
  }

  public function setRemainingAmountAttribute($value)
  {
    return $this->attributes['remaining_amount'] = Money::{$this->currency ?? config('money.defaults.currency')}($value)->getAmount() * 100;
  }

  public function getStatusAttribute()
  {
    $value = $this->getRawOriginal('status');
    if ($value == 'Terminated' || $value == 'Paused' || $value == 'Draft') return $value;
    //elseif(!$this->start_date) return 'Draft'; // just for extra protection otherwise start date is required in otherthan draft.
    // elseif ($this->end_date && $this->end_date->isPast()) return 'Expired';
    // elseif ($this->start_date->isFuture()) return 'Not started';
    // elseif ($this->end_date && now() > $this->end_date->subWeeks(2)) return 'About To Expire';
    // elseif (now() >= $this->start_date) return 'Active';

    if ($this->end_date === null && $this->start_date === null)
      return '';

    if ($this->end_date == null && $this->start_date) {
      if ($this->start_date->isSameDay(today())) {
        return "Active";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } else {
        return "Expired";
      }
    } else {
      if ($this->end_date->isPast()) {
        return "Expired";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } elseif (now()->diffInDays($this->end_date) <= 30) {
        return "About To Expire";
      } else {
        return "Active";
      }
    }
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

  public function invoices(): HasMany
  {
    return $this->hasMany(Invoice::class);
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
        $q->where('status', 'Active')->where('start_date', '<=', now())->where('end_date', '>=', now());//->where('end_date', '>=', now()->addWeeks(2));
      } elseif (request()->filter_status == 'About To Expire') {
        $q->where('status', 'Active')->where('end_date', '>', now())->where('end_date', '<', now()->addMonth());
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

  public function category(): BelongsTo
  {
    return $this->belongsTo(ContractCategory::class);
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
    return $this->hasMany(ContractPhase::class);
  }

  public function directPhases(): HasMany
  {
    return $this->hasMany(ContractPhase::class)->whereNull('stage_id')->where('is_committed', true);
  }

  public function initialStage(): HasOne
  {
    return $this->hasOne(ContractStage::class)->where('stage_type', 'Initial Stage');
  }

  public function initialStageMilstones(): HasManyThrough
  {
    return $this->hasManyThrough(ContractPhase::class, ContractStage::class, 'contract_id', 'stage_id')->where('stage_type', 'Initial Stage');
  }

  public function stages(): HasMany
  {
    return $this->hasMany(ContractStage::class);
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

  public function saveEventLog(ContractUpdateRequest|Request $request, Contract $contract): void
  {
    $start_date_updated = false;
    $end_date_updated = false;
    $value_updated = $contract->value != $request->value;
    $c_start_date = $contract->start_date ? $contract->start_date->format('d M, Y') : 'NULL';
    $c_end_date = $contract->end_date ? $contract->end_date->format('d M, Y') : 'NULL';
    if (($contract->end_date && !$request->end_date)
      || (!$contract->end_date && $request->end_date)
      || ($contract->end_date && $request->end_date && !$contract->end_date->isSameDay($request->end_date))
    ) {
      $end_date_updated = true;
    }
    if (($contract->start_date && !$request->start_date)
      || (!$contract->start_date && $request->start_date)
      || ($contract->start_date && $request->start_date && !$contract->start_date->isSameDay($request->start_date))
    ) {
      $start_date_updated = true;
    }

    //End Date Revised
    if (!$start_date_updated && $end_date_updated && !$value_updated) {
      // dd(Carbon::parse($request->end_date)->format('d M, Y'));
      $contract->events()->create([
        'event_type' => 'End Date Revised',
        'modifications' => [
          'old' => ['end_date' => $contract->end_date],
          'new' => ['end_date' => $request->end_date],
        ],
        'description' => 'Contract End Date Revised From ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y'),
        'admin_id' => auth()->id(),
      ]);
    }
    // Start Date Revised
    else if ($start_date_updated && !$end_date_updated && !$value_updated) {
      $contract->events()->create([
        'event_type' => 'Start Date Revised',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date],
          'new' => ['start_date' => $request->start_date],
        ],
        'description' => 'Contract Start Date Revised From ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y'),
        'admin_id' => auth()->id(),
      ]);
    }

    // Rescheduled
    else if ($start_date_updated && $end_date_updated && !$value_updated) {
      $contract->events()->create([
        'event_type' => 'Rescheduled',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date, 'end_date' => $contract->end_date],
          'new' => ['start_date' => $request->start_date, 'end_date' => $request->end_date],
        ],
        'description' => 'Contract Rescheduled From Start Date: ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y') . ' and From End Date:  ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y'),
        'admin_id' => auth()->id(),
      ]);
    }

    // Amount Increased
    else if (!$start_date_updated && !$end_date_updated && $value_updated && $request->value > $contract->value) {
      $contract->events()->create([
        'event_type' => 'Amount Increased',
        'modifications' => [
          'old' => ['value' => $contract->value],
          'new' => ['value' => $request->value],
        ],
        'description' => 'Contract Amount Increased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // Amount Decreased
    else if (!$start_date_updated && !$end_date_updated && $value_updated && $request->value < $contract->value) {
      $contract->events()->create([
        'event_type' => 'Amount Decreased',
        'modifications' => [
          'old' => ['value' => $contract->value],
          'new' => ['value' => $request->value],
        ],
        'description' => 'Contract Amount Decreased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // Rescheduled And Amount Increased
    else if ($start_date_updated && $end_date_updated && $value_updated && $request->value > $contract->value) {
      $contract->events()->create([
        'event_type' => 'Rescheduled And Amount Increased',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date, 'end_date' => $contract->end_date, 'value' => $contract->value],
          'new' => ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'value' => $request->value],
        ],
        'description' => 'Contract Rescheduled From Start Date: ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y') . ' and From End Date:  ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y') . ' and Amount Increased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // Rescheduled And Amount Decreased
    else if ($start_date_updated && $end_date_updated && $value_updated && $request->value < $contract->value) {
      $contract->events()->create([
        'event_type' => 'Rescheduled And Amount Decreased',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date, 'end_date' => $contract->end_date, 'value' => $contract->value],
          'new' => ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'value' => $request->value],
        ],
        'description' => 'Contract Rescheduled From Start Date: ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y') . ' and From End Date:  ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y') . ' and Amount Decreased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // Start Date Revised And Amount Increased
    else if ($start_date_updated && !$end_date_updated && $value_updated && $request->value > $contract->value) {
      $contract->events()->create([
        'event_type' => 'Start Date Revised And Amount Increased',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date, 'value' => $contract->value],
          'new' => ['start_date' => $request->start_date, 'value' => $request->value],
        ],
        'description' => 'Contract Start Date Revised From ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y') . ' and Amount Increased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // Start Date Revised And Amount Decreased
    else if ($start_date_updated && !$end_date_updated && $value_updated && $request->value < $contract->value) {
      $contract->events()->create([
        'event_type' => 'Start Date Revised And Amount Decreased',
        'modifications' => [
          'old' => ['start_date' => $contract->start_date, 'value' => $contract->value],
          'new' => ['start_date' => $request->start_date, 'value' => $request->value],
        ],
        'description' => 'Contract Start Date Revised From ' . $c_start_date . ' to ' . Carbon::parse($request->start_date)->format('d M, Y') . ' and Amount Decreased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // End Date Revised And Amount Increased
    else if (!$start_date_updated && $end_date_updated && $value_updated && $request->value > $contract->value) {
      $contract->events()->create([
        'event_type' => 'End Date Revised And Amount Increased',
        'modifications' => [
          'old' => ['end_date' => $contract->end_date, 'value' => $contract->value],
          'new' => ['end_date' => $request->end_date, 'value' => $request->value],
        ],
        'description' => 'Contract End Date Revised From ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y') . ' and Amount Increased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }

    // End Date Revised And Amount Decreased
    else if (!$start_date_updated && $end_date_updated && $value_updated && $request->value < $contract->value) {
      $contract->events()->create([
        'event_type' => 'End Date Revised And Amount Decreased',
        'modifications' => [
          'old' => ['end_date' => $contract->end_date, 'value' => $contract->value],
          'new' => ['end_date' => $request->end_date, 'value' => $request->value],
        ],
        'description' => 'Contract End Date Revised From ' . $c_end_date . ' to ' . Carbon::parse($request->end_date)->format('d M, Y') . ' and Amount Decreased From ' . $contract->value . ' to ' . $request->value,
        'admin_id' => auth()->id(),
      ]);
    }
  }

  public function formatPhaseValue($value)
  {
    return Money::{$this->currency ?? config('money.defaults.currency')}($value)->getAmount();
  }

  public function formatStageValue($value)
  {
    return Money::{$this->currency ?? config('money.defaults.currency')}($value)->getAmount();
  }

  public function getPrintableValueAttribute()
  {
    return Money::{$this->currency ?? config('money.defaults.currency')}($this->value, true)->format();
  }

  public function program(): BelongsTo
  {
    return $this->belongsTo(Program::class);
  }

  public function resume(): void
  {
    $contract = $this;
    $discription = 'Contract Resumed' . (auth()->id() ? ' Manually' : ' By System');

    // update the contract end date if it has paused more than 1 day
    $event = $contract->events()->where('event_type', 'Paused')->whereNull('applied_at')->first();

    $pausedDays = now()->diffInDays($event->modifications['pause_date']);
    // if($pausedDays > 0){
    $contract->update(['end_date' => $contract->end_date ? $contract->end_date->addDays($pausedDays) : null, 'status' => 'Active']);
    $discription .=  ' after ' . $pausedDays . ' days and new end date is ' . $contract->end_date->format('d M, Y');
    // }

    $contract->events()->create([
      'event_type' => 'Resumed',
      'modifications' => ['pause_until' => null, 'pause_date' => null],
      'description' => $discription,
      'admin_id' => auth()->id(),
    ]);

    // mark the pause event as applied
    $contract->events()->where('event_type', 'Paused')->whereNull('applied_at')->update(['applied_at' => now()]);
  }

  public function changeRequests(): HasMany
  {
    return $this->hasMany(ContractChangeRequest::class);
  }

  public function uploadedDocs()
  {
    return $this->morphMany(UploadedKycDoc::class, 'doc_requestable');
  }
}
