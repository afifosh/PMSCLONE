<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'company_id',
    'program_id',
    'type_id',
    'category_id',
    'pipeline_id',
    'scorecard_id',
    'form_id',
    'description',
    'start_at',
    'end_at'
  ];

  protected $casts = [
    'start_at' => 'datetime: M d, Y',
    'end_at' => 'datetime: M d, Y',
    'created_at' => 'datetime: M d, Y',
    'updated_at' => 'datetime: M d, Y'
  ];

  /**
   * Company that will submit the application
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /**
   * Program belongs to this application
   */
  public function program()
  {
    return $this->belongsTo(Program::class);
  }

  /**
   * Type of application
   */
  public function type()
  {
    return $this->belongsTo(ApplicationType::class);
  }

  /**
   * Category of application
   */
  public function category()
  {
    return $this->belongsTo(ApplicationCategory::class);
  }

  /**
   * Pipeline used for this application
   */
  public function pipeline()
  {
    return $this->belongsTo(ApplicationPipeline::class);
  }

  /**
   * Scorecard used for this application
   */
  public function scorecard()
  {
    return $this->belongsTo(ApplicationScoreCard::class);
  }

  /**
   * Form used for this application submission
   */
  public function form()
  {
    return $this->belongsTo(Form::class);
  }

  /**
   * Applications users, which can intereact with the application
   */
  public function users()
  {
    return $this->belongsToMany(Admin::class, ApplicationUser::class);
  }

  /**
   * scope to get applications that belongs to the current logged in user
   */
  public function scopeMine($query)
  {
    return $query->when(!auth()->user()->isSuperAdmin(), function ($query) {
      return $query->whereHas('users', function ($q) {
        $q->where('admin_id', auth()->id());
      });
    });
  }

  /**
   * Polymorphic models that can submit this application
   */
  public function submitters()
  {
    return $this->morphToMany(ApplicationSubmitter::class, 'submitter');
  }
}
