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
    return $value / 1000;
  }

  public function setValueAttribute($value)
  {
    return $this->attributes['value'] = Money::{$this->currency ?? config('money.defaults.currency')}($value)->getAmount() * 1000;
  }

  public function getRemainingAmountAttribute()
  {
    return $this->value - $this->stages->sum('stage_amount');
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
      if (request()->filter_status == 'Draft') return $q->where('contracts.status', 'Draft');
      else if (request()->filter_status == 'Not started') {
        $q->where('start_date', '>', now());
      } elseif (request()->filter_status == 'Expired') {
        $q->where('contracts.status', 'Active')->where('end_date', '<', now());
      } elseif (request()->filter_status == 'Terminated') {
        $q->where('contracts.status', 'Terminated');
      } elseif (request()->filter_status == 'Paused') {
        $q->where('contracts.status', 'Paused');
      } elseif (request()->filter_status == 'Active') {
        $q->where('contracts.status', 'Active')->where('start_date', '<=', now())->where('end_date', '>=', now()); //->where('end_date', '>=', now()->addWeeks(2));
      } elseif (request()->filter_status == 'About To Expire') {
        $q->where('contracts.status', 'Active')->where('end_date', '>', now())->where('end_date', '<', now()->addMonth());
      }
    })->when(request()->companies, function ($q) {
      $q->where('assignable_type', Company::class)->where('assignable_id', request()->companies);
    })
    ->when(request()->search_q, function ($q) {
      $q->where(function ($q) {
        $q->where('subject', 'like', '%' . request()->search_q . '%')
          ->orWhereHas('phases', function ($q) {
            $q->where('name', 'like', '%' . request()->search_q . '%');
          });
      });
    })->when(request()->contract_type, function ($q) {
        $q->where('type_id', request()->contract_type);
    })->when(request()->contract_category, function ($q) {
        $q->where('category_id', request()->contract_category);
    })->when(request()->projects, function ($q) {
      $q->whereHas('project', function ($q) {
        $q->where('id', request()->projects);
      });
    })->when(request()->programs, function ($q) {
      $q->whereHas('program', function ($q) {
        $q->where('id', request()->programs);
      });
    })->when(request()->has('haspayments'), function($q){
      $q->has('invoices.payments');
    })->when(request()->date_range && @explode(' to ', request()->date_range)[0], function($q){
      try {
        $date = Carbon::parse(explode(' to ', request()->date_range)[0]);
        $q->where('start_date', '>=', $date);
      } catch (\Exception $e) {
      }
    })->when(request()->date_range && @explode(' to ', request()->date_range)[1], function($q){
      try {
        $date = Carbon::parse(explode(' to ', request()->date_range)[1]);
        $q->where('end_date', '<=', $date);
      } catch (\Exception $e) {
      }
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

  public function remaining_cost($stage_cost = 0)
  {
    return $this->remaining_amount + $stage_cost;
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

  public function requestedDocs()
  {
    return KycDocument::where('status', 1) // active
      ->where('workflow', 'Contract Required Docs') // workflow
      ->whereIn('client_type', array_merge(['Both'], ($this->assignable instanceof Company ?  [$this->assignable->type] : []))) // filter by client type
      ->where(function ($q) { // filter by contract type
        $q->when($this->type_id, function ($q) {
          $q->whereHas('contractTypes', function ($q) {
            $q->where('contract_types.id', $this->type_id);
          })->orHas('contractTypes', '=', 0);
        });
      })
      ->where(function ($q) { // filter by contract category
        $q->when($this->category_id, function ($q) {
          $q->whereHas('contractCategories', function ($q) {
            $q->where('contract_categories.id', $this->category_id);
          })->orHas('contractCategories', '=', 0);
        });
      });
  }

  public function pendingDocs()
  {
    return $this->requestedDocs()
      ->whereDoesntHave('uploadedDocs', function ($q) { // filter by uploaded docs
        $q->where('doc_requestable_id', $this->id)
          ->where('doc_requestable_type', Contract::class)
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }
}
