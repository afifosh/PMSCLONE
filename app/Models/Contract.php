<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Spatie\Comments\Models\Concerns\HasComments;

class Contract extends BaseModel
{
  use HasFactory, SoftDeletes, HasEnum, HasComments;

  protected $fillable = [
    'category_id',
    'type_id',
    'project_id',
    'assignable_type',
    'assignable_id',
    'program_id',
    'signature_date',
    'invoice_method',
    'refrence_id',
    'subject',
    'subject_ar',
    'currency',
    'value',
    'start_date',
    'end_date',
    'description',
    'visible_to_client',
    'status'
  ];

  protected $appends = ['status', 'printable_value'];

  /**
   * Contract statuses stored in DB
   */
  public const BASE_STATUSES = [
    0 => 'Draft',
    1 => 'Active',
    2 => 'Paused',
    3 => 'Terminated',
    4 => 'Early Completed',
    5 => 'Completed',
    6 => 'Cancelled',
  ];

  /**
   * Derived statuses from base statuses
   */
  public const STATUSES = [
    0 => 'Draft',
    1 => 'Active',
    2 => 'Paused',
    3 => 'Terminated',
    4 => 'Early Completed',
    5 => 'Completed',
    6 => 'Cancelled',
    7 => 'Not started',
    8 => 'Expired',
    9 => 'About To Expire',
  ];

  protected $casts = [
    'visible_to_client' => 'boolean',
    'signature_date' => 'datetime:d M, Y',
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];


  /*
  * @constant FILES_PATH The path prefix to store uploaded Docs.
  */
  public const FILES_PATH = 'contracts';

  // protected static function booted()
  // {
  //     // contract should be accessible by auth admin
  //     // static::addGlobalScope(new ContractACLAccessibleByAuth);
  // }

  /**
   * prioritize arabic subject over english subject
   */
  public function getSubjectAttribute($value)
  {
    return $this->subject_ar ? $this->subject_ar : $value;
  }

  public function getValueAttribute($value)
  {
    return $value / 1000;
  }

  public function setValueAttribute($value)
  {
    return $this->attributes['value'] = moneyToInt($value);
  }

  public function getRemainingAmountAttribute()
  {
    return $this->value - $this->phases->sum('total_cost');
  }

  public function getAllUsersStagesReviewStatusWithlastReviewDate()
  {
    // Assuming the Contract model has a 'stages' relationship that contains many ContractStage
    $stages = $this->stages;
    $users = $this->program->users;

    $allUsersStagesStatus = [];

    foreach ($users as $user) {
      $userStagesStatus = [];

      foreach ($stages as $stage) {
        // Get the status for this stage for this user
        $status = $stage->getUserReviewStatusWithlastReviewDate($user->id);

        // Add stage information for reference
        $userStagesStatus[] = [
          'stage_id' => $stage->id,
          'stage_name' => $stage->name,
          'status' => $status['status'],
          'last_review_date' => $status['last_review_date'],
        ];
      }

      // Add user information and their stages status to the final array
      $allUsersStagesStatus[] = [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'stages_status' => $userStagesStatus,
      ];
    }

    return $allUsersStagesStatus;
  }

  /**
   * All The admins which can review this contract (having active ACL)
   */
  public function canReviewedBy()
  {
    return Admin::canReviewContract($this->id, $this->program_id);
  }

  /**
   * Users who have reviewed this contract
   */
  public function reviewedBy()
  {
    return $this->belongsToMany(Admin::class, 'reviews', 'reviewable_id', 'user_id')
      ->where('reviewable_type', self::class);
  }

  /**
   * Users who reviewed all the phases of this contract
   */
  public function usersCompletedPhasesReview()
  {
    $phases_count = $this->phases_count;

    return Admin::whereHas('addedReviews', function ($q) {
      $q->where('reviewable_type', ContractPhase::class)->whereHas('phase', function ($q) {
        $q->where('contract_id', $this->id);
      });
    }, '>=', $phases_count)
      ->when($phases_count == 0, function ($q) {
        $q->where('id', 0); // just to return empty collection
      });
  }

  public function myReviewedPhases()
  {
    return $this->phases()->whereHas('reviews', function ($q) {
      $q->where('user_id', auth()->id());
    });
  }

  /**
   * Get logged in user's review progress (%) based on phases of this contract.
   */
  public function myPhasesReviewProgress()
  {
    return $this->myReviewedPhases_count / $this->phases_count * 100;
  }

  /**
   * conracts which are reviewed by admin
   */
  public function scopeCompletelyReviewedBy($q, $admin_id)
  {
    return $q->has('phases')->whereDoesntHave('phases', function ($q) use ($admin_id) {
      $q->whereDoesntHave('reviews', function ($q) use ($admin_id) {
        $q->where('user_id', $admin_id);
      });
    });
  }

  /**
   * contracts which are partialy reviewed by admin
   */
  public function scopePartiallyReviewedBy($query, $admin_id)
  {
    return $query->whereHas('phases', function ($q) use ($admin_id) {
      $q->whereHas('reviews', function ($q) use ($admin_id) {
        $q->where('user_id', $admin_id);
      });
    })
      // also have some phases which are not reviewed by this admin
      ->whereHas('phases', function ($q) use ($admin_id) {
        $q->whereDoesntHave('reviews', function ($q) use ($admin_id) {
          $q->where('user_id', $admin_id);
        });
      });
  }

  /**
   * contracts which are not reviewed by admin
   */
  public function scopeNotReviewedBy($q, $admin_id)
  {
    return $q->has('phases')->whereDoesntHave('phaseReviews', function ($q) use ($admin_id) {
      $q->where('user_id', $admin_id);
    });
  }

  public function getStatusAttribute()
  {
    $value = $this->getRawOriginal('status');
    if ($value == 0) return self::BASE_STATUSES[0];
    if ($value != 1 && in_array($value, array_keys(self::BASE_STATUSES))) return self::BASE_STATUSES[$value];
    //elseif(!$this->start_date) return 'Draft'; // just for extra protection otherwise start date is required in otherthan draft.
    // elseif ($this->end_date && $this->end_date->isPast()) return 'Expired';
    // elseif ($this->start_date->isFuture()) return 'Not started';
    // elseif ($this->end_date && now() > $this->end_date->subWeeks(2)) return 'About To Expire';
    // elseif (now() >= $this->start_date) return 'Active';

    if ($this->end_date === null && $this->start_date === null)
      return '';

    if ($this->end_date == null && $this->start_date) {
      if ($this->start_date->isSameDay(today())) {
        return self::STATUSES[1]; // active
      } elseif ($this->start_date->isFuture()) {
        return self::STATUSES[7]; // not started
      } else {
        return self::STATUSES[8]; // expired
      }
    } else {
      if ($this->end_date->isPast()) {
        return self::STATUSES[8]; // expired
      } elseif ($this->start_date->isFuture()) {
        return self::STATUSES[7]; // not started
      } elseif (now()->diffInDays($this->end_date) <= 30) {
        return self::STATUSES[9]; // about to expire
      } else {
        return self::STATUSES[1]; // active
      }
    }
  }

  public function getPossibleStatuses()
  {
    $status = $this->getRawOriginal('status');
    if ($status == self::STATUSES[1]) {
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
    return $q->when(request()->has('filter_status'), function ($q) {
      if (request()->filter_status === '0') {
        return $q->where('contracts.status', 0);
      } // draft
      else if (request()->filter_status == 7) {
        $q->where('start_date', '>', now()); // not started
      } elseif (request()->filter_status == 8) { // expired
        $q->where('contracts.status', 1)->where('end_date', '<', now());
      } elseif (request()->filter_status == 3) {
        $q->where('contracts.status', 3); // terminated
      } elseif (request()->filter_status == 2) {
        $q->where('contracts.status', 2); // paused
      } elseif (request()->filter_status == 1) {
        $q->where('contracts.status', 1)->where('start_date', '<=', now())->where('end_date', '>=', now()); //->where('end_date', '>=', now()->addWeeks(2));
      } elseif (request()->filter_status == 9) { // about to expire
        $q->where('contracts.status', 1)->where('end_date', '>', now())->where('end_date', '<', now()->addMonth());
      } elseif (request()->filter_status == 4) { // early completed
        $q->where('contracts.status', 4);
      } elseif (request()->filter_status == 5) { // completed
        $q->where('contracts.status', 5);
      } elseif (request()->filter_status == 6) { // cancelled
        $q->where('contracts.status', 6);
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
      })->when(request()->has('haspayments'), function ($q) {
        $q->has('invoices.payments');
      })->when(request()->has('hasinv'), function ($q) {
        $q->has('invoices');
      })->when(request()->has('has-ta-inv'), function ($q) {
        $q->has('invoices.authorityInvoice');
      })->when(request()->date_range && @explode(' to ', request()->date_range)[0], function ($q) {
        try {
          $date = Carbon::parse(explode(' to ', request()->date_range)[0]);
          $q->where('start_date', '>=', $date);
        } catch (\Exception $e) {
        }
      })->when(request()->date_range && @explode(' to ', request()->date_range)[1], function ($q) {
        try {
          $date = Carbon::parse(explode(' to ', request()->date_range)[1]);
          $q->where('end_date', '<=', $date);
        } catch (\Exception $e) {
        }
      })->when(request('phase_review_status'), function ($q) {
        $reviewStatus = request('review_status');

        // Join with reviews table
        $q->leftJoin('reviews', function ($join) {
          $join->on('reviews.reviewable_id', '=', 'contracts.id')
            ->where('reviews.reviewable_type', '=', Contract::class);
        });

        if ($reviewStatus == 'reviewed') {
          // Filter contracts that have at least one review
          $q->whereNotNull('reviews.id');
        } elseif ($reviewStatus == 'not_reviewed') {
          // Filter contracts that do not have any reviews
          $q->whereNull('reviews.id');
        }

        // Remove the reviews table from select to avoid column name conflicts
        $q->select('contracts.*');
      })->when(request('reviewed_by') && request('reviewed_by') !== 'all', function ($q) {
        $q->whereHas('reviews', function ($subQuery) {
          $subQuery->where('user_id', request('reviewed_by'));
        });
      })->when(request()->contracts, function ($q) {
        $q->where('contracts.id', request()->contracts);
      })
      // does not have access list rule for this admin
      ->when(request()->has('dnh_acl_rule_for') && request()->dnh_acl_rule_for, function ($q) {
        $q->whereDoesntHave('program', function ($q) {
          $q->accessibleByAdmin(request()->dnh_acl_rule_for);
        })
          ->whereDoesntHave('directACLRules', function ($q) {
            $q->where('admin_id', request()->dnh_acl_rule_for);
          });
      })
      // has reviewed phase or can review phase.
      ->when(request()->phase_reviewer, function ($q) {
        $q->validAccessibleByAdmin(request()->phase_reviewer)
          ->orWhereHas('phaseReviews', function ($q) {
            $q->where('user_id', request()->phase_reviewer);
          });
      })
      ->when(request()->has('phase_review_status') && request()->phase_reviewer, function ($q) {
        $q->when(request()->phase_review_status == 'reviewed', function ($q) {
          $q->completelyReviewedBy(request()->phase_reviewer);
        })
          ->when(request()->phase_review_status == 'not_reviewed', function ($q) {
            $q->notReviewedBy(request()->phase_reviewer);
          })
          ->when(request()->phase_review_status == 'partially_reviewed', function ($q) {
            $q->partiallyReviewedBy(request()->phase_reviewer);
          });
      })
      ->when(request()->dependent_2_col == 'creating-inv-type' && request()->get('dependent_2'), function ($q) {
        // invoice creating dependent filter
        $q->when(request()->get('dependent_2') == 'Partial Invoice', function ($q) {
          // user is creating partial invoice, query the contracts which has allowable phases.
          $q->whereHas('phases', function ($q) {
            $q->where('is_allowable_cost', 1);
          });
        })
          ->when(request()->get('dependent_2') == 'Regular', function ($q) {
            // user is creating regular invoice, query the contractw which has regular phases.
            $q->whereHas('phases', function ($q) {
              $q->where('is_allowable_cost', 0);
            });
          });
      })
      ->when(request()->dependent_2_col == 'change_request_type' && request()->dependent_2, function ($q) {
        // select2 filter for change request type
        $q->when(request()->dependent_2 == 'pause-contract', function ($q) {
          $q->where('status', 1); // active
        })
          ->when(request()->dependent_2 == 'resume-contract', function ($q) {
            $q->where('status', 2); // paused
          })
          ->when(request()->dependent_2 == 'terminate-contract', function ($q) {
            $q->where('status', 1); // active
          })
          ->when(request()->dependent_2 == 'contract-completed', function ($q) {
            $q->where('status', 1); // active
          })
          ->when(request()->dependent_2 == 'update-terms', function ($q) {
            $q->whereIn('status', [1, 2]); // active
          });
      })
      ->when(request()->contract_review_status && request()->contract_reviewer, function ($q) {
        // select2 filter for contract review status
        $q->when(request()->contract_review_status == 'reviewed', function ($q) {
          // contracts which are reviewed by the given admin
          $q->whereHas('reviews', function ($q) {
            $q->where('user_id', request()->contract_reviewer);
          });
        })
          ->when(request()->contract_review_status == 'not_reviewed', function ($q) {
            // contracts which are not reviewed by the given admin
            $q->whereDoesntHave('reviews', function ($q) {
              $q->where('user_id', request()->contract_reviewer);
            });
          });
      });
  }

  public function scopeApplyRequestFiltersOLD($q)
  {
    return $q->when(request()->filter_status, function ($q) {
      if (request()->filter_status == 0) return $q->where('contracts.status', 0); // draft
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
      })->when(request()->has('haspayments'), function ($q) {
        $q->has('invoices.payments');
      })->when(request()->has('hasinv'), function ($q) {
        $q->has('invoices');
      })->when(request()->date_range && @explode(' to ', request()->date_range)[0], function ($q) {
        try {
          $date = Carbon::parse(explode(' to ', request()->date_range)[0]);
          $q->where('start_date', '>=', $date);
        } catch (\Exception $e) {
        }
      })->when(request()->date_range && @explode(' to ', request()->date_range)[1], function ($q) {
        try {
          $date = Carbon::parse(explode(' to ', request()->date_range)[1]);
          $q->where('end_date', '<=', $date);
        } catch (\Exception $e) {
        }
      })->when(request('review_status'), function ($q) {
        $reviewStatus = request('review_status');

        // Join with reviews table
        $q->leftJoin('reviews', function ($join) {
          $join->on('reviews.reviewable_id', '=', 'contracts.id')
            ->where('reviews.reviewable_type', '=', Contract::class);
        });

        if ($reviewStatus == 'reviewed') {
          // Filter contracts that have at least one review
          $q->whereNotNull('reviews.id');
        } elseif ($reviewStatus == 'not_reviewed') {
          // Filter contracts that do not have any reviews
          $q->whereNull('reviews.id');
        }

        // Remove the reviews table from select to avoid column name conflicts
        $q->select('contracts.*');
      })->when(request('reviewed_by') && request('reviewed_by') !== 'all', function ($q) {
        $q->whereHas('reviews', function ($subQuery) {
          $subQuery->where('user_id', request('reviewed_by'));
        });
      });
  }

  /**
   * Explicit ACL rules for this contract instead of inherited from program
   */
  public function directACLRules()
  {
    return $this->hasMany(AdminAccessList::class, 'accessable_id', 'id')->where('accessable_type', self::class);
  }

  /**
   * Explicit ACL rules for this contract instead of inherited from program and not expired or revoked
   */
  public function validDirectACLRules()
  {
    return $this->hasMany(AdminAccessList::class, 'accessable_id', 'id')
      ->where('accessable_type', self::class)
      ->where('is_revoked', false)
      ->where(function ($q) {
        $q->whereNull('granted_till')
          ->orWhere('granted_till', '>=', now());
      });
  }

  /**
   * Explicit ACL rules for this contract instead of inherited from program and expired or revoked
   */
  public function invalidDirectACLRules()
  {
    return $this->hasMany(AdminAccessList::class, 'accessable_id', 'id')
      ->where('accessable_type', self::class)
      ->where(function ($q) {
        $q->where('granted_till', '<', now())
          ->orWhere('is_revoked', true);
      });
  }



  /**
   * ACL rules inherited from program
   */
  public function programACLRules()
  {
    return $this->hasMany(AdminAccessList::class, 'accessable_id', 'program_id')->where('accessable_type', Program::class);
  }

  /**
   * ACL rules inherited from program and not expired or revoked
   */
  public function validProgramACLRules()
  {
    return $this->hasMany(AdminAccessList::class, 'accessable_id', 'program_id')
      ->where('accessable_type', Program::class)
      ->where('is_revoked', false)
      ->where(function ($q) {
        $q->whereNull('granted_till')
          ->orWhere('granted_till', '>=', now());
      });
  }

  /**
   * Contracts which have ACL rules for this admin
   * @param $admin_id int
   * @return QueryBuilder
   */
  public function scopeHasAccessListOfAdmin($q, $admin_id)
  {
    $q->whereHas('program', function ($q) use ($admin_id) {
      $q->accessibleByAdmin($admin_id);
    })
      ->orWhereHas('directACLRules', function ($q) use ($admin_id) {
        $q->where('admin_id', $admin_id);
      })
      ->with([
        'program.pivotAccessLists' => function ($q) use ($admin_id) {
          $q->where('admin_id', $admin_id);
        },
        'directACLRules' => function ($q) use ($admin_id) {
          $q->where('admin_id', $admin_id);
        }
      ]);
  }

  /**
   * Contracts which have Active ACL rule for the given admin and not expired or revoked
   */
  public function scopeValidAccessibleByAdmin($q, $admin_id)
  {
    $q->where(function ($q) use ($admin_id) {
      $q->whereHas('validProgramACLRules', function ($q) use ($admin_id) {
        $q->where('admin_id', $admin_id);
      })
        ->orWhereHas('validDirectACLRules', function ($q) use ($admin_id) {
          $q->where('admin_id', $admin_id);
        });
    })
      ->whereDoesntHave('invalidDirectACLRules', function ($q) use ($admin_id) {
        $q->where('admin_id', $admin_id);
      });
  }

  public function reviews()
  {
    return $this->morphMany(Review::class, 'reviewable');
  }

  public function phaseReviews()
  {
    return $this->hasManyThrough(Review::class, ContractPhase::class, 'contract_id', 'reviewable_id', 'id', 'id')->where('reviewable_type', ContractPhase::class);
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

  public function getPrintableValueAttribute()
  {
    return Money($this->value, $this->currency, true);
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
      })
      ->where(function ($q) { // required_at && required_at_type
        $q->whereNull('required_at') // if required_at is null then at any time this is required
          // if required_at is not null then check the required_at_type
          ->orWhere(function ($q) {
            $q->where('required_at', '>=', today())
              ->where('required_at_type', 'Before');
          })
          ->orWhere(function ($q) {
            $q->where('required_at', '<=', today())
              ->where('required_at_type', 'After');
          })
          ->orWhere(function ($q) {
            $q->where('required_at', today())
              ->where('required_at_type', 'On');
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

  /**
   * Documents requested to upload against this Contract which are uploaded and not expired
   */
  public function uploadedValidDocs()
  {
    return $this->requestedDocs()
      ->whereHas('uploadedDocs', function ($q) { // filter by uploaded docs
        $q->where('doc_requestable_id', $this->id)
          ->where('doc_requestable_type', Contract::class)
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }

  /**
   * Documents requested to upload against this Contract which are uploaded but expired.
   */
  public function uploadedExpiredDocs()
  {
    return $this->requestedDocs()
      ->whereHas('uploadedDocs', function ($q) { // filter by uploaded docs
        $q->where('doc_requestable_id', $this->id)
          ->where('doc_requestable_type', Contract::class)
          ->where('expiry_date', '<', today());
      })

      // does not have valid uploaded docs
      ->whereDoesntHave('uploadedDocs', function ($q) { // filter by uploaded docs
        $q->where('doc_requestable_id', $this->id)
          ->where('doc_requestable_type', Contract::class)
          ->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', today());
          });
      });
  }

  public function releaseInvoicesRetentions($account_id)
  {
    $this->invoices->each(function ($invoice) use ($account_id) {
      $invoice->releaseRetention($account_id);
    });
  }

  /*
  * This string will be used in notifications on what a new comment
  * was made.
  */
  public function commentableName(): string
  {
    return 'Contract: ' . $this->contract->subject;
  }

  /*
  * This URL will be used in notifications to let the user know
  * where the comment itself can be read.
  */
  public function commentUrl(): string
  {
    return '#';
  }

  public function contract_parties()
  {
    return $this->hasMany(ContractParty::class);
  }

  /**
   * Get the downpayment invoices which can be deducted from this contract.
   */
  public function deductableDownpayments(): HasMany
  {
    return $this->hasMany(Invoice::class)->where('type', 'Down Payment')->where('status', 'Paid');
  }

  public function getSankeyFundsData()
  {
    $data = [];

    $this->load('stages.taxes');

    //sample data
    // { from: "A", to: "E", value: 1, id:"A0-0" },
    $this->stages->each(function ($stage) use (&$data) {
      // $data[] = [
      //   'from' => $this->subject,
      //   'to' => $stage->name,
      //   'value' => $stage->stage_amount,
      //   'id' => 's'.$stage->id,
      // ];

      $stage->taxes()->each(function ($tax) use (&$data, $stage) {
        $data[] = [
          'from' => $stage->name,
          'phase' => $tax->contractPhase->name,
          'to' => ($tax->tax->name . ' - ' . $tax->tax->categoryShortName()),
          'value' => $tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount,
          'id' => 't' . $stage->id . '-' . $tax->tax->id,
        ];
      });

      // $stage->phases->each(function ($phase) use (&$data, $stage) {
      //   // $data[] = [
      //   //   'from' => $stage->name,
      //   //   'to' => $phase->name,
      //   //   'value' => $phase->total_cost,
      //   //   'id' => 'p'.$phase->id.'-'.$phase->id,
      //   // ];

      //   $phase->pivotTaxes->each(function ($tax) use (&$data, $stage) {
      //     $data[] = [
      //       'from' => $stage->name,
      //       'to' => ($tax->tax->name . ' - ' . $tax->tax->categoryName()),
      //       'value' => $tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount,
      //       'id' => 't'.$stage->id.'-'.$tax->tax->id,
      //     ];
      //   });
      // });
    });
    return $data;
  }
}
