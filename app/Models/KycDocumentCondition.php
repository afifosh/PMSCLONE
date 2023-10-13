<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocumentCondition extends Model
{
  use HasFactory;

  protected $fillable = [
    'kyc_document_id',
    'conditionable_id',
    'conditionable_type'
  ];

  public function kycDocument()
  {
    return $this->belongsTo(KycDocument::class);
  }

  public function conditionable()
  {
    return $this->morphTo();
  }
}
