<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailService extends Model
{
    use HasFactory;

    /**
     * Fields that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'service_label',
        'service',
        'is_active',
    ];

    /**
     * An email service has many fields
     * 
     * @return HasMany
     */
    public function emailServiceFields()
    {
        return $this->hasMany(EmailServiceField::class);
    }
}
