<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
  use HasFactory;

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
  ];

  public const TYPES = ['date', 'email', 'file', 'number', 'tel','text', 'textarea'];

  public const VALIDATIONS = [
    'date' => ['date'],
    'email' => ['email', 'max:255'],
    'file' => ['file', 'mimeTypes:image/*'],
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
}
