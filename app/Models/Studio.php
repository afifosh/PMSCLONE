<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Avatar;

class Studio extends Model
{
    use HasFactory, SoftDeletes;
    public const DT_ID = 'studios_datatable';

    protected $fillable = [
        'added_by',
        'country_id',
        'state_id',
        'city_id',
        'name',
        'website',
        'avatar',
        'email',
        'address',
        'zip',
        'phone',
        'language',
        'timezone',
        'currency',
        'status',
    ];

    protected $dates = ['deleted_at'];
    protected $appends = ['avatar'];

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

    public function addedBy()
    {
        return $this->belongsTo(Admin::class, 'added_by');
    }
   
    public function getAvatarAttribute($value)
    {
      if (!$value)
        return Avatar::create($this->name)->toBase64();
      return @Storage::url($value);
    }
  
    public function getPhotoUrlAttribute()
    {
      return $this->avatar;
    }
  

}
