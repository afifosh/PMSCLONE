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

  public const TYPES = ['text', 'number', 'email', 'tel', 'textarea', 'file'];

  public const VALIDATIONS = [
    'text' => ['string', 'max:255'],
    'number' => ['numeric', 'max:255'],
    'email' => ['email', 'max:255'],
    'tel' => ['numeric', 'max:255'],
    'textarea' => ['string', 'max:255'],
    'file' => ['file', 'mimeTypes:image/*']
  ];

  protected $casts = [
    'fields' => 'array',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];
}
