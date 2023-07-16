<?php

namespace App\Traits;

use App\Models\TimelineLog;
use Illuminate\Support\Facades\Log;

trait HasLogs
{
  public function logs()
  {
    return $this->morphMany(TimelineLog::class, 'logable', 'logable_type', 'logable_id');
  }

  public function createLog($log, $data = [])
  {
    $actioner = ['actioner_id' => null, 'actioner_type' => null];
    if (auth()->check()) {
      $actioner['actioner_id'] = auth()->id();
      $actioner['actioner_type'] = auth()->user()::class;
      $data['ip'] = request()->ip();
    }
    return $this->logs()->create(['log' => $log, 'data' => $data,] + $actioner);
  }

  public static function boot()
  {
    parent::boot();

    // Created event listener
    static::created(function ($model) {
      // create log entry
      $model->createLog(class_basename($model) . ' Created', $model->toArray());
    });

    // Updated event listener
    static::updated(function ($model) {
      // create log entry
      $model->createLog(class_basename($model) . ' Updated', $model->toArray());
    });

    // Deleted event listener
    static::deleted(function ($model) {
      // create log entry
      $model->createLog(class_basename($model) . ' Deleted', $model->toArray());
    });
  }
}
