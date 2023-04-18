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

use Webpatser\Countries\Countries;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Country extends Countries
{
    /**
     * Indicates if the model has timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Name attribute accessor
     *
     * Supports translation from language file
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function name() : Attribute
    {
        return Attribute::get(function ($value) {
            $customKey = 'custom.country.' . $value;
            if (Lang::has($customKey)) {
                return __($customKey);
            } elseif (Lang::has($value)) {
                return __($value);
            }

            return $value;
        });
    }
}
