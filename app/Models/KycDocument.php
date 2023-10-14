<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    'client_type',
    'invoice_type',
    'required_at',
    'required_at_type' // Before, After, On
  ];

  /**
   * input fields types which are allowed to be added in a document form
   */
  public const TYPES = ['date', 'email', 'file', 'number', 'tel', 'text', 'textarea'];

  /**
   * validation rules for each input field type
   */
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
    'required_at' => 'datetime: d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];


  /**
   * The contract types from which this document is required.
   * null means all contract types
   * polymorphic, inverse many-to-many relationship
   *
   * @return MorphedByMany
   */
  public function contractTypes()
  {
    return $this->morphedByMany(ContractType::class, 'conditionable', 'kyc_doc_conditions');
  }

  /**
   * The contract categories from which this document is required.
   * null means all contract categories
   * polymorphic, inverse many-to-many relationship
   *
   * @return MorphedByMany
   */
  public function contractCategories()
  {
    return $this->morphedByMany(ContractCategory::class, 'conditionable', 'kyc_doc_conditions');
  }

  /**
   * The contracts from which this document is required.
   * null means all contracts
   * polymorphic, inverse many-to-many relationship
   *
   * @return MorphedByMany
   */
  public function contracts()
  {
    return $this->morphedByMany(Contract::class, 'conditionable', 'kyc_doc_conditions');
  }

  /**
   * Uploaded Documents against this required document
   * one-to-many relationship
   *
   * @return HasMany
   */
  public function uploadedDocs(): HasMany
  {
    return $this->hasMany(UploadedKycDoc::class, 'kyc_doc_id');
  }
}
