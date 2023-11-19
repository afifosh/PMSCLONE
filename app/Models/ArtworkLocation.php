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
    'warehouse_id',
    'contract_id',
    'added_by',
    'datein',
    'dateout',
    'remarks',
    'is_current',
  ];

  protected $casts = [
    'datein' => 'datetime:d M, Y',
    'dateout' => 'datetime:d M, Y',    
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
   * The contract by which artwork is placed.
   */
  public function contract(): BelongsTo
  {
    return $this->belongsTo(Contract::class);
  }

  /**
   * The location where this artwork is placed.
   */
  public function location(): BelongsTo
  {
    return $this->belongsTo(Location::class);
  }

   /**
   * The warehouse where this artwork is placed.
   * Art work can be placed in warehouse or location. But not both.
   */
  public function warehouse(): BelongsTo
  {
    return $this->belongsTo(Warehouse::class);
  }

  /**
   * The admin who added this artwork at this location.
   */
  public function addedBy(): BelongsTo
  {
    return $this->belongsTo(Admin::class, 'added_by', 'id');
  }
}
