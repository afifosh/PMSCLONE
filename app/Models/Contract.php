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
use App\Support\Money;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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


  public function usersWhoCompletedAllPhases()
  {
      // // Retrieve the total number of phases for this contract.
      // $totalPhases = $this->phases()->count();
  
      // // Subquery to get user IDs and their count of distinct reviewed phases.
      // // Note that we need the fully qualified class name for `reviewable_type`.
      // $subQuery = Review::select('user_id')
      //     ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
      //     ->where('reviewable_type', get_class($this->phases()->getRelated())) // assuming 'phases' is the name of the relation method
      //     ->whereIn('reviewable_id', $this->phases()->pluck('id'))
      //     ->groupBy('user_id')
      //     ->havingRaw('phases_count = ?', [$totalPhases]); // use havingRaw to filter users who reviewed all phases
  
      // // Main query to get admins who have completed all phases.
      // // Join the subquery to filter users based on the phases count.
      // $adminsWhoCompletedAllPhases = Admin::select('admins.*')
      //     ->joinSub($subQuery, 'reviewed_phases', function ($join) {
      //         $join->on('admins.id', '=', 'reviewed_phases.user_id');
      //     })
      //     ->get();

      // Calculate the total number of phases for the current object
      $totalPhases = $this->phases()->count();

      // Subquery to get user IDs and their count of distinct reviewed phases.
      // We ensure the reviews are only for the specific phases related to the current object.
      $subQuery = Review::select('user_id')
          ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
          ->where('reviewable_type', get_class($this->phases()->getRelated())) // Ensure we're looking at the correct reviewable type
          ->whereIn('reviewable_id', $this->phases()->pluck('id')->toArray()) // Select only reviews for these phase IDs
          ->groupBy('user_id') // Group the results by user ID
          ->having('phases_count', $totalPhases); // Filter to users who reviewed all phases

      // You can now use the $subQuery to get the users who have reviewed all phases.
      // For example, you might want to get these users:
      $adminsWhoCompletedAllPhases = Admin::whereIn('id', $subQuery->pluck('user_id'))->get();  

      return $adminsWhoCompletedAllPhases;
  }
  
  public function usersWhoNotCompletedAllPhases()
  {
      // // Retrieve the total number of phases for this contract.
      // $totalPhases = $this->phases()->count();
  
      // // Subquery to get user IDs and their count of distinct reviewed phases.
      // // Note that we need the fully qualified class name for `reviewable_type`.
      // $subQuery = Review::select('user_id')
      //     ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
      //     ->where('reviewable_type', get_class($this->phases()->getRelated())) // assuming 'phases' is the name of the relation method
      //     ->whereIn('reviewable_id', $this->phases()->pluck('id'))
      //     ->groupBy('user_id');
  
      // // Main query to get admins who have NOT completed all phases.
      // // We are doing a LEFT JOIN here with the users table and filtering out the ones that have a phases_count equal to totalPhases.
      // $adminsWhoNotCompletedAllPhases = Admin::select('admins.*')
      //     ->leftJoinSub($subQuery, 'reviewed_phases', function ($join) {
      //         $join->on('admins.id', '=', 'reviewed_phases.user_id');
      //     })
      //     // We use 'whereRaw' here to add a SQL raw where clause, checking if phases_count is not the total or there's no review (NULL).
      //     ->whereRaw('(reviewed_phases.phases_count IS NULL OR reviewed_phases.phases_count < ?)', [$totalPhases])
      //     ->get();
  
      // Calculate the total number of phases for the current object
      $totalPhases = $this->phases()->count();

      // Subquery to get user IDs and their count of distinct reviewed phases
      // for the specific phases related to the current object.
      $subQuery = Review::select('user_id')
          ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
          ->where('reviewable_type', get_class($this->phases()->getRelated())) // Check against the correct reviewable type
          ->whereIn('reviewable_id', $this->phases()->pluck('id')->toArray()) // Only select reviews for these phase IDs
          ->groupBy('user_id') // Group the results by user ID
          ->having('phases_count', '<', $totalPhases); // Filter to users who have NOT reviewed all phases

      // Now, get only the admins who have not reviewed all phases.
      $adminsWhoNotCompletedAllPhases = Admin::whereIn('id', $subQuery->pluck('user_id'))->get();

      return $adminsWhoNotCompletedAllPhases;
  }
    
  public function getAdminsWhoDidNotReviewAnyPhase()
  {
      // Get IDs of all phases associated with this contract's program
      $phaseIds = $this->phases()->pluck('id');
      
      // Get query for all admin user IDs associated with this contract's program
      $programAdminIdsQuery = ProgramUser::ofProgram($this->program_id)->select('admin_id');
      
      // Get query for all admin user IDs who have made a review for any of the phase IDs
      $adminsWhoMadeReviewsQuery = Review::whereIn('reviewable_id', $phaseIds)
          ->where('reviewable_type', get_class($this->phases()->getRelated()))
          ->select('user_id')
          ->distinct();
      
      // Get the list of admin users who have not made any review entries for any phase of this contract's program
      $adminsWithoutReviews = Admin::whereNotIn('id', $adminsWhoMadeReviewsQuery)
          ->whereIn('id', $programAdminIdsQuery)
          ->get();
  
      return $adminsWithoutReviews;
  }

  public function getAllUsersStagesReviewStatusWithlastReviewDate()
  {
      // Assuming the Contract model has a 'stages' relationship that contains many ContractStage
      $stages = $this->stages;
      $users = $this->program->users; 

      $program = $this->program; // This gives you the program object
      $parentProgram = $program->parent; // This gives you the parent program object
      
      // Get the users of this program
      $programUsers = $program->users;
  
      // If there is a parent program, get those users as well
      $parentProgramUsers = collect();
      if ($parentProgram) {
          $parentProgramUsers = $parentProgram->users;
      }
  
      // Combine the collections, ensuring there are no duplicates
      $allUsers = $programUsers->merge($parentProgramUsers)->unique('id');

      $allUsersStagesStatus = [];

      foreach ($allUsers as $user) {
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

  public function getAdminsWhoDidNotCompleteOrDidNotReviewAnyPhase()
  {
      // Get a collection of admins who have not reviewed any phase
      $adminsWithoutAnyReviews = $this->getAdminsWhoDidNotReviewAnyPhase();
  
      // Get a collection of admins who have not completed all phases
      $adminsWhoDidNotCompleteAllPhases = $this->usersWhoNotCompletedAllPhases();
  
      // Combine the two collections and remove duplicates to get a list of unique admins
      $combinedAdmins = $adminsWithoutAnyReviews->merge($adminsWhoDidNotCompleteAllPhases)->unique('id');
  
      return $combinedAdmins;
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
    })->when(request()->has('hasinv'), function($q){
      $q->has('invoices');
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
    })->when(request()->contracts, function($q){
      $q->where('contracts.id', request()->contracts);
    });
}
  
  public function scopeApplyRequestFiltersOLD($q)
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
    })->when(request()->has('hasinv'), function($q){
      $q->has('invoices');
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

  public function reviews()
  {
      return $this->morphMany(Review::class, 'reviewable');
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

  public function releaseInvoicesRetentions()
  {
    $this->invoices->each(function ($invoice) {
      $invoice->releaseRetention();
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
}
