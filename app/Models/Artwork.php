<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Avatar;
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

}
