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

namespace App\Models;

use App\Innoclapps\Models\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailAccountMessageHeader extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model has timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value', 'header_type'];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'message_id' => 'int',
    ];

    /**
     * Get the mapped attribute
     *
     * We will map the header into a appropriate header class
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function mapped() : Attribute
    {
        return Attribute::get(fn () => new $this->header_type($this->name, $this->value));
    }
}
