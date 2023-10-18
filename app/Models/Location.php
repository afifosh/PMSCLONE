<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'address',
    'latitude',
    'longitude',
    'zoomLevel',
    'country_id',
    'city_id',
    'state_id',
    'added_by',
    'is_public',
    'owner_type',
    'owner_id',
    'status',
  ];

  protected $casts = [
    'latitude' => 'float',
    'longitude' => 'float',
    'zoomLevel' => 'int',
  ];

  /**
   * The country of this location.
   */
  public function country(): BelongsTo
  {
    return $this->belongsTo(Country::class);
  }

  /**
   * The city of this location.
   */
  public function city(): BelongsTo
  {
    return $this->belongsTo(City::class);
  }

  /**
   * The state of this location.
   */
  public function state(): BelongsTo
  {
    return $this->belongsTo(State::class);
  }

  /**
   * The admin who added this location.
   */
  public function addedBy(): BelongsTo
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }

  /**
   * The artworks placed at this location.
   */
  public function artworks(): BelongsToMany
  {
    return $this->belongsToMany(Artwork::class, 'artwork_locations', 'location_id', 'artwork_id')
      ->withPivot(['moved_from', 'added_by', 'added_till'])
      ->withTimestamps();
  }

  /**
   * The owner of this location.
   * the model that owns this location, e.g. Company, PartnerCompany, etc.
   */
  public function owner(): MorphTo
  {
    return $this->morphTo();
  }
}
