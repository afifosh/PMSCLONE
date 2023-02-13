<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'password_expire_days',
        'timeout_warning_seconds',
        'timeout_after_seconds',
    ];
}
