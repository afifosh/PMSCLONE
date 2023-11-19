<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Avatar;
use Illuminate\Support\Facades\Storage;


class Artist extends Model
{
    use HasFactory;

    public const DT_ID = 'artists_datatable';
    public const ARTIST_PATH = 'artworks-images';

    protected $fillable = [
        'added_by',
        'country_id',
        'state_id',
        'city_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'avatar',
        'website',
        'job_title',
        'gender',
        'address',
        'zip_code',
        'language',
        'timezone',
        'currency',
        'birth_date',
        'death_date',
        'status',
    ];

     protected $casts = [
       'verified_at' => 'datetime:d M, Y',
       'created_at' => 'datetime:d M, Y',
       'updated_at' => 'datetime:d M, Y'
     ];

     protected $appends = ['avatar'];

     public function getAvatarAttribute($value)
     {
       if (!$value)
         return Avatar::create($this->full_name)->toBase64();
       return @Storage::url($value);
     }

     public function getPhotoUrlAttribute()
     {
       return $this->avatar;
     }

     public function getNameAttribute()
     {
       return $this->full_name;
     }

     public function getFullNameAttribute()
     {
       return ucwords($this->first_name . ' ' . $this->last_name);
     }

     public function addedBy()
     {
       return $this->belongsTo(Admin::class, 'added_by', 'id');
     }

    // Define an accessor for the age
    public function getAgeAttribute()
    {
        $birthDate = Carbon::parse($this->attributes['birth_date']);
        return $birthDate->age;

    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class, 'artwork_artists');
    }


    public function mediums()
    {
        return $this->morphMany(Medium::class, 'mediumable');
    }

}
