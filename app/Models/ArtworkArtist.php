<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkArtist extends Model
{
  use HasFactory;

  protected $fillable = [
    'artwork_id',
    'artist_id'
  ];

  public function artwork()
  {
    return $this->belongsTo(Artwork::class);
  }

  public function artist()
  {
    return $this->belongsTo(Artist::class);
  }
}
