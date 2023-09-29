<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocumentContractCategory extends Model
{
  use HasFactory;

  protected $fillable = [
    'kyc_document_id',
    'contract_category_id'
  ];

  public function kycDocument()
  {
    return $this->belongsTo(KycDocument::class);
  }

  public function contractCategory()
  {
    return $this->belongsTo(ContractCategory::class);
  }
}
