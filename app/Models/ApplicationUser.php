<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationUser extends Model
{
  use HasFactory;

  protected $fillable = [
    'application_id',
    'admin_id',
    'role'
  ];

  protected $casts = [
    'created_at' => 'datetime: M d, Y',
    'updated_at' => 'datetime: M d, Y'
  ];

  public const ROLE = [
    '1' => 'Evaluator',
    '2' => 'Reviewer',
  ];
  /**
   * Application that belongs to this user
   */
  public function application()
  {
    return $this->belongsTo(Application::class);
  }

  /**
   * Admin that belongs to this application
   */
  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
