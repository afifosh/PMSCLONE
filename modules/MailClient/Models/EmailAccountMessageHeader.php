<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Core\Models\Model;

class EmailAccountMessageHeader extends Model
{
    /**
     * Indicates if the model has timestamps
     *
     * @var bool
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
     */
    public function mapped(): Attribute
    {
        return Attribute::get(fn () => new $this->header_type($this->name, $this->value));
    }
}
