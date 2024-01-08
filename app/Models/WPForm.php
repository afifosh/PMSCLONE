<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WPForm extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'field_id']; // Add other fields as needed

    // Define relationship to fields if you have a Field model
    public function fields()
    {
        return $this->hasMany(WPFormField::class);
    }

    /**
     * Get the next available field ID and increment by one.
     *
     * @return int
     */
    public function nextFieldId()
    {
        $maxFieldId = $this->fields()->max('id');
        return $maxFieldId ? $maxFieldId + 1 : 1;
    }

}
