<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadedKycDoc extends Model
{
  use HasFactory, CompanyApprovalBaseLogic;

  protected $table = 'uploaded_kyc_docs';

  protected $fillable = [
    'company_id',
    'kyc_doc_id',
    'fields',
    'uploader_id',
    'uploader_type',
    'expiry_date',
  ];

  protected $casts = [
    'fields' => 'array',
    'expiry_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  protected $appends = ['status'];

  public const FILE_PATH = 'requested-docs';

  public function getStatusAttribute()
  {
    if ($this->expiry_date && $this->expiry_date->endOfDay()->isPast()) {
      return 'Expired';
    }
    return 'Active';
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function kycDoc()
  {
    return $this->belongsTo(KycDocument::class);
  }

  public function requestedDoc()
  {
    return $this->kycDoc();
  }

  public static function getModelName()
  {
    return 'KYC Doc';
  }

  public function docRequestable()
  {
    return $this->morphTo('doc_requestable', 'doc_requestable_id', 'doc_requestable_type');
  }

  public function uploader()
  {
    return $this->morphTo();
  }

  public function versions(): HasMany
  {
    return $this->hasMany($this::class, 'kyc_doc_id', 'kyc_doc_id')
    ->whereColumn('uploaded_kyc_docs.doc_requestable_type', 'uploaded_kyc_docs.doc_requestable_type')
    ->whereColumn('uploaded_kyc_docs.doc_requestable_id', 'uploaded_kyc_docs.doc_requestable_id');
  }
}
