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
    'fields',
  ];

  public const TYPES = ['date', 'email', 'file', 'number', 'tel','text', 'textarea'];

  public const VALIDATIONS = [
    'date' => ['date'],
    'email' => ['email', 'max:255'],
    'file' => ['file', 'mimeTypes:image/*'],
    'number' => ['numeric', 'max:255'],
    'tel' => ['numeric', 'max:255'],
    'text' => ['string', 'max:255'],
    'textarea' => ['string', 'max:255']
  ];

  protected $casts = [
    'fields' => 'array',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];
}
