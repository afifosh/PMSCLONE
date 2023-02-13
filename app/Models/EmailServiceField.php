<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailServiceField extends Model
{
    use HasFactory;

    /**
     * Fields that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'email_service_id',
        'field_name',
        'field_value'
    ];

    /**
     * An email service field belongs to an email service
     * 
     * @return BelongsTo
     */
    public function emailService()
    {
        return $this->belongsTo(EmailService::class);
    }
}
