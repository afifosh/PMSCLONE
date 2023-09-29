<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KycDocument extends Model
{
  use HasFactory;

  public const TEMP_PATH = 'kyc-docs/temp';

  protected $fillable = [
    'title',
    'required_from',
    'status',
    'is_mendatory',
    'description',
    'is_expirable',
    'expiry_date_title',
    'is_expiry_date_required',
    'fields',
    'workflow',
    'client_type'
  ];

  public const TYPES = ['date', 'email', 'file', 'number', 'tel', 'text', 'textarea'];

  public const VALIDATIONS = [
    'date' => ['date'],
    'email' => ['email', 'max:255'],
    'file' => ['string', 'max:255'],
    'number' => ['digits_between:0,255'],
    'tel' => ['numeric', 'max:255'],
    'text' => ['string', 'max:255'],
    'textarea' => ['string', 'max:255']
  ];

  protected $casts = [
    'is_expirable' => 'boolean',
    'is_mendatory' => 'boolean',
    'is_expiry_date_required' => 'boolean',
    'fields' => 'array',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function contractTypes(): BelongsToMany
  {
    return $this->belongsToMany(ContractType::class, KycDocumentContractType::class);
  }

  public function contractCategories(): BelongsToMany
  {
    return $this->belongsToMany(ContractCategory::class, KycDocumentContractCategory::class);
  }

  public function uploadedDocs(): HasMany
  {
    return $this->hasMany(UploadedKycDoc::class, 'kyc_doc_id');
  }
}
