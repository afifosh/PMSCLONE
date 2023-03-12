<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Fields that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'context',
        'settingable_type',
        'settingable_id',
    ];
}
