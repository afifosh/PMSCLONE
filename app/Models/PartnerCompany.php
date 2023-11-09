<?php

namespace App\Models;

use App\Traits\HasEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Avatar;

class PartnerCompany extends BaseModel
{
    use HasFactory, HasEnum;

    public const DT_ID = 'partner-companies-dataTable';

    protected $fillable = ['name', 'website', 'phone', 'status'];

    public const STATUS = [
      'active' => 'Active',
      'inactive' => 'Inactive',
    ];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function getAvatarAttribute($value)
    {
      if(!$value)
        return Avatar::create($this->name)->toBase64();
      return $value;
    }

    public function departments()
    {
      return $this->hasMany(CompanyDepartment::class, 'company_id', 'id');
    }

    /**
   * Get all the location owned by this company. (Locations for placing artworks)
   */
  public function artworkLocations()
  {
    return $this->morphMany(Location::class, 'owner', 'owner_type', 'owner_id');
  }

  public function scopeApplyRequestFilters($query)
  {
    //
  }
}
