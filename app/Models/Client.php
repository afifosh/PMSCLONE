<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Avatar;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
  use HasFactory, HasEnum;

  protected $fillable = [
    'country_id',
    'first_name',
    'last_name',
    'phone',
    'email',
    'avatar',
    'address',
    'state',
    'zip_code',
    'language',
    'status',
    'last_seen',
    'is_online',
    'timezone',
    'currency',
  ];

  public function getAvatarAttribute($value)
  {
    if (!$value)
      return Avatar::create($this->full_name)->toBase64();
    return @Storage::url($value);
  }

  public function getFullNameAttribute()
  {
    return ucwords($this->first_name . ' ' . $this->last_name);
  }

  public function country()
  {
    return $this->belongsTo(Country::class);
  }

  public function contracts(): MorphMany
  {
    return $this->morphMany(Contract::class, 'assignable');
  }
}
