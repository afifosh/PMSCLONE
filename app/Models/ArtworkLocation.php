<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkLocation extends Model
{
  use HasFactory;

  protected $fillable = [
    'artwork_id',
    'location_id',
    'moved_from',
    'added_by',
    'added_till',
  ];

  protected $casts = [
    'added_till' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  /**
   * The artwork that is placed at this location.
   */
  public function artwork(): BelongsTo
  {
    return $this->belongsTo(Artwork::class);
  }

  /**
   * The location where this artwork is placed.
   */
  public function location(): BelongsTo
  {
    return $this->belongsTo(Location::class);
  }

  /**
   * The location from where this artwork is moved.
   */
  public function movedFrom(): BelongsTo
  {
    return $this->belongsTo(ArtworkLocation::class, 'moved_from', 'id');
  }

  /**
   * The admin who added this artwork at this location.
   */
  public function addedBy(): BelongsTo
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }
}
