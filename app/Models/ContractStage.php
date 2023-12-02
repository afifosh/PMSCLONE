<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStage extends BaseModel
{
  use HasFactory;

  protected $fillable = [
    'name',
    'contract_id',
  ];

  protected $casts = [
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  protected $appends = ['status', 'stage_amount', 'start_date', 'due_date'];

  public function reviewersWhoCompleted()
  {
      return User::whereHas('reviews.phase', function ($query) {
          $query->where('stage_id', $this->id);
      }, '=', $this->phases()->count())->get();
  }

  public function reviewersWhoCompletedAllPhases()
  {
      $totalPhases = $this->phases()->count();

      $subQuery = Review::select('user_id')
          ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
          ->where('reviewable_type', ContractPhase::class) // Assuming that the reviewable type is ContractPhase
          ->whereIn('reviewable_id', $this->phases->pluck('id')->toArray())
          ->groupBy('user_id')
          ->having('phases_count', '=', $totalPhases);

      $usersWhoCompletedAllPhases = Admin::whereIn('id', $subQuery->pluck('user_id'))->get();

      return $usersWhoCompletedAllPhases;
  }

  public function reviewersWhoDidNotCompleteAllPhases()
  {
      $totalPhases = $this->phases()->count();

      $subQuery = Review::select('user_id')
          ->selectRaw('COUNT(DISTINCT reviewable_id) as phases_count')
          ->where('reviewable_type', ContractPhase::class) // Assuming that the reviewable type is ContractPhase
          ->whereIn('reviewable_id', $this->phases->pluck('id')->toArray())
          ->groupBy('user_id')
          ->having('phases_count', '<', $totalPhases);

      $usersWhoDidNotCompleteAllPhases = Admin::whereIn('id', $subQuery->pluck('user_id'))->get();

      return $usersWhoDidNotCompleteAllPhases;
  }

  public function hasUserCompletedStage($userId)
  {
      $totalPhases = $this->phases()->count();

      $reviewedPhasesCount = Review::where('user_id', $userId)
          ->where('reviewable_type', ContractPhase::class) // Replace with your phase model if different
          ->whereIn('reviewable_id', $this->phases->pluck('id')->toArray())
          ->distinct()
          ->count('reviewable_id');

      return $reviewedPhasesCount === $totalPhases;
  }

  public function getUserReviewStatus($userId)
  {
      $totalPhases = $this->phases()->count();

      // If there are no phases, we consider the stage as not started or not applicable.
      if ($totalPhases === 0) {
          return 'Not applicable';
      }

      $reviewedPhasesCount = Review::where('user_id', $userId)
          ->where('reviewable_type', ContractPhase::class) // Make sure this matches your actual phase model
          ->whereIn('reviewable_id', $this->phases->pluck('id')->toArray())
          ->distinct()
          ->count('reviewable_id');

      if ($reviewedPhasesCount === $totalPhases) {
          return 'Completed';
      } elseif ($reviewedPhasesCount > 0) {
          return 'In progress';
      } else {
          return 'Not started';
      }
  }

  public function getUserReviewStatusWithlastReviewDate($userId)
  {
      $totalPhases = $this->phases()->count();

      // If there are no phases, we consider the stage as not started or not applicable.
      if ($totalPhases === 0) {
          return ['status' => 'Not applicable', 'last_review_date' => null];
      }

      // Get the count of distinct phases reviewed by the user
      $reviews = Review::where('user_id', $userId)
          ->where('reviewable_type', ContractPhase::class) // Ensure you're using the correct phase model
          ->whereIn('reviewable_id', $this->phases->pluck('id')->toArray())
          ->distinct()
          ->get(['reviewable_id', 'created_at']);

      $reviewedPhasesCount = $reviews->count();
      $lastReviewDate = $reviews->max('created_at');

      $status = 'Not started';
      if ($reviewedPhasesCount === $totalPhases) {
          $status = 'Completed';
      } elseif ($reviewedPhasesCount > 0) {
          $status = 'In progress';
      }

      // Return both the status and the last review date
      return [
          'status' => $status,
          'last_review_date' => $lastReviewDate ? $lastReviewDate->format('Y-m-d') : null
      ];
  }

  public function getAllUsersStagesReviewStatusWithlastReviewDate()
  {
      // Assuming the Contract model has a 'program' relationship and 'program' has 'users' relationship
      $users = $this->program->users;
      $allUsersStagesStatus = [];

      foreach ($users as $user) {
          // Get the status for all stages for this user
          $userStagesStatus = $this->getUserStagesReviewStatus($user->id);

          // Add user information for reference
          $allUsersStagesStatus[] = [
              'user_id' => $user->id,
              'user_name' => $user->name,
              'stages_status' => $userStagesStatus
          ];
      }

      return $allUsersStagesStatus;
  }


  public function getStatusAttribute()
  {
    // if($this->due_date->isPast()) return 'Expired';
    // elseif($this->start_date->isFuture()) return 'Not started';
    // elseif(now() > $this->due_date->subMonth()) return 'About To Expire';
    // elseif(now() >= $this->start_date) return 'Active';
    if ($this->due_date === null && $this->start_date === null)
      return '';

    if ($this->due_date == null && $this->start_date) {
      if ($this->start_date->isSameDay(today())) {
        return "Active";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } else {
        return "Expired";
      }
    } else {
      if ($this->due_date->isPast()) {
        return "Expired";
      } elseif ($this->start_date->isFuture()) {
        return "Not Started";
      } elseif (now()->diffInDays($this->due_date) <= 30) {
        return "About To Expire";
      } else {
        return "Active";
      }
    }
  }

  public function getStageAmountAttribute()
  {
    return $this->phases->sum('estimated_cost');
  }

  public function getStartDateAttribute()
  {
    return $this->phases->min('start_date');
  }

  public function getDueDateAttribute()
  {
    return $this->phases->max('due_date');
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function phases()
  {
    return $this->hasMany(ContractPhase::class, 'stage_id');
  }

}
