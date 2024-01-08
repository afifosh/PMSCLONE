<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WPFormField extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = ['wp_form_id', 'type', 'label', 'description'];

    /**
     * Relationship with the WPForm model
     */
    public function wpForm()
    {
        return $this->belongsTo(WPForm::class);
    }

}
