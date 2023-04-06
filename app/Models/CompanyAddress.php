<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

  protected $fillable = [
    'name',
    'country_id',
    'address_line_1',
    'address_line_2',
    'address_line_3',
    'website',
    'city',
    'state',
    'province',
    'postal_code',
    'zip',
    'phone',
    'fax',
    'email',
    'latitude',
    'longitude',
    'address_type',
    'status',
  ];

  protected $casts = [
    'address_type' => 'array',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function updateIfDirty($attributes)
  {
    $this->fill($attributes);
    if ($this->isDirty()) {
      return $this->save();
    }
  }

  public static function getFields()
  {
    return [
      'Address Name' => 'name',
      'Country' => 'country_id',
      'Address Line 1' => 'address_line_1',
      'Address Line 2' => 'address_line_2',
      'Address Line 3' => 'address_line_3',
      'Website' => 'website',
      'City/Town/Locality' => 'city',
      'State' => 'state',
      'Province' => 'province',
      'Postal Code' => 'postal_code',
      'Zip Code' => 'zip',
      'Phone' => 'phone',
      'Fax' => 'fax',
      'Email' => 'email',
      'Latitude' => 'latitude',
      'Longitude' => 'longitude',
      'Address Type' => 'address_type'
    ];
  }
}
