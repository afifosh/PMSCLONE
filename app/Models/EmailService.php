<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailService extends Model
{
    use HasFactory;

    /**
     * Fields that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'label',
        'name',
        'is_active',
        'sent_from_name',
        'sent_from_address',
        'transport',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'access_key_id',
        'secret_access_key',
        'region',
        'domain_name',
        'api_key',
    ];
}
