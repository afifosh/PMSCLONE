<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
  use HasFactory, Tenantable, CompanyApprovalBaseLogic;

  protected $fillable = [
    'name',
    'logo',
    'website',
    'locality_type',
    'geographical_coverage',
    'date_founded',
    'duns_number',
    'no_of_employees',
    'legal_form',
    'description',
    'facebook_url',
    'twitter_url',
    'linkedin_url',
    'youtube_url',
    'sa_company_name',
    'parent_company',
    'subsidiaries',
  ];

  protected $casts = [
    'geographical_coverage' => 'array',
    'subsidiaries' => 'array',
  ];

  public const LegalForms = [
    '1' => 'Establishment',
    '2' => 'Partnerships',
    '3' => 'Private Limited Company',
    '4' => 'Join venture/Consortium',
    '5' => 'Corporations',
    '6' => 'Limited Collective Partnership',
    '7' => 'Limited Liability Company (LLC)',
  ];

  public const NoOfEmployee = [
    "1-9" => '1-9 Employees',
    "10-49" => '10-49 Employees',
    "50-99" => '50-99 Employees',
    "100-499" => '100-499 Employees',
    "500+" => '500+ Employees'
  ];

  public const LocalityTypes = [
    '1' => 'Foreign/International',
    '2' => 'Local',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function modifications()
  {
    return $this->morphMany(Modification::class, 'modifiable');
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
      'Company Name' => 'name',
      'Logo' => 'logo',
      'Website' => 'website',
      'Locality Type' => 'locality_type',
      'Geographical Coverage' => 'geographical_coverage',
      'Year Founded' => 'date_founded',
      'DUNS Number' => 'duns_number',
      'No. Of Employees' => 'no_of_employees',
      'Legal Form' => 'legal_form',
      'Description' => 'description',
      'Facebook URL' => 'facebook_url',
      'Twitter URL' => 'twitter_url',
      'LinkedIn URL' => 'linkedin_url',
      'YouTube URL' => 'youtube_url',
      'SA Company Name' => 'sa_company_name',
      'Parent Company Name' => 'parent_company',
      'Subsidiaries' => 'subsidiaries',
    ];
  }
}
