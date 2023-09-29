<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedKycDoc extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

  protected $table = 'uploaded_kyc_docs';

  protected $fillable = [
    'company_id',
    'kyc_doc_id',
    'fields',
    'expiry_date',
  ];

  protected $casts = [
    'fields' => 'array',
    'expiry_date' => 'date',
  ];

  public const FILE_PATH = 'kyc-docs/company';

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function kycDoc()
  {
    return $this->belongsTo(KycDocument::class);
  }

  public static function getModelName()
  {
    return 'KYC Doc';
  }

  public function docRequestable()
  {
    return $this->morphTo('doc_requestable', 'doc_requestable_id', 'doc_requestable_type');
  }
}
