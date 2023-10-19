<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Avatar;

class Medium extends Model
{
  use HasFactory;

  protected $table = 'mediums';

  protected $fillable = ['name'];

  public const DT_ID = 'mediums_datatable';

  protected $casts = [
    'verified_at' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  public function getFormattedNameAttribute()
  {
      return $this->name;
  }

  public function addedBy()
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }


}
