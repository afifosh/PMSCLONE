<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Models;

class CustomFieldOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_order',
        'swatch_color',
    ];

    /**
     * Indicates if the model has timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'custom_field_id' => 'int',
        'display_order'   => 'int',
    ];

    /**
     * A custom field option belongs to custom field
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }

    /**
     * Determine if the model touches a given relation.
     * The custom field option touches all parent models
     *
     * For example, when record that is using custom field with options is updated
     * we need to update the record updated_at column.
     *
     * In this case, tha parent must use timestamps too.
     *
     * @param string $relation
     *
     * @return boolean
     */
    public function touches($relation)
    {
        return true;
    }
}