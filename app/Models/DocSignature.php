<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocSignature extends Model
{
  use HasFactory;

  protected $fillable = [
    'uploaded_kyc_doc_id',
    'signer_type',
    'signer_id',
    'signer_position',
    'signed_at',
    'is_signature',
  ];

  protected $casts = [
    'signed_at' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y'
  ];

  /**
   * Signer of the document
   */
  public function signer()
  {
    return $this->morphTo();
  }

  public function uploadedDoc()
  {
    return $this->belongsTo(UploadedKycDoc::class);
  }
}
