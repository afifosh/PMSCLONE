<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocumentContractType extends Model
{
  use HasFactory;

  protected $fillable = [
    'kyc_document_id',
    'contract_type_id'
  ];

  public function kycDocument()
  {
    return $this->belongsTo(KycDocument::class);
  }

  public function contractType()
  {
    return $this->belongsTo(ContractType::class);
  }
}
