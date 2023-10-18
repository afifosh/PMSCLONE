<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Avatar;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Artwork extends Model
{
  use HasFactory;

  public const DT_ID = 'artworks_datatable';

  protected $fillable = [
    'year',
    'medium',
    'dimension',
    'title',
    'featured_image',
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
   * latest location will be the current location
   */
  public function locations(): BelongsToMany
  {
    return $this->belongsToMany(Location::class, ArtworkLocation::class)
      ->withPivot('moved_from', 'added_by', 'added_till') // pivot table's columns will be available when accessing the relationship
      ->withTimestamps()
      ->latest(); // order by pivot table's created_at column
  }

  /**
   * The current location of the artwork.
   */
  public function currentLocation()
  {
    return $this->locations()->latest()->first();
  }
}
