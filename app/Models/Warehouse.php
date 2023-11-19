<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    // Mass assignable attributes

    protected $fillable = [
        'name',
        'location_id',
        'added_by',
        'owner_type',
        'owner_id',
        'status',
      ];

      protected $casts = [
        'created_at' => 'datetime:d M, Y',
        'updated_at' => 'datetime:d M, Y'
      ];
    // Relationship with Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * The admin who added this location.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'added_by', 'id');
    }

    // Polymorphic relationship for owner
    public function owner()
    {
        return $this->morphTo();
    }
}
