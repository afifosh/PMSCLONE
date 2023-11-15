<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Avatar;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Artwork extends Model
{
  use HasFactory;

  public const DT_ID = 'artworks_datatable';

  protected $fillable = [
    'title',
    'medium_id',
    'program_id',
    'year',
    'dimension',
    'featured_image',
    'description',
    'added_by'
  ];

  protected $casts = [
    'verified_at' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  protected $appends = ['featured_image'];

  public function getFeaturedImageAttribute($value)
  {
    if (!$value)
      return Avatar::create($this->title)->toBase64();
    return @Storage::url($value);
  }

  public function getPhotoUrlAttribute()
  {
    return $this->featured_image;
  }

  public function getNameAttribute()
  {
    return $this->title;
  }


  public function addedBy()
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }

  public function medium()
  {
    return $this->belongsTo(Medium::class);
  }

  /**
   * Locations of the artwork.
   */
  public function locations(): HasMany
  {
    return $this->hasMany(ArtworkLocation::class);
  }

  /**
   * latest location (pivot) of the artwork.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function latestLocation()
  {
    return $this->hasOne(ArtworkLocation::class)->where('is_current', true);
  }

  public function program(): BelongsTo
  {
    return $this->belongsTo(Program::class);
  }

  public function artists(): BelongsToMany
  {
    return $this->belongsToMany(Artist::class, 'artwork_artists');
  }
}
