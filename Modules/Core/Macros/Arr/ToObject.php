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

namespace Modules\Core\Macros\Arr;

class ToObject
{
    public function __invoke($array)
    {
        if (! is_array($array) && ! is_object($array)) {
            return new \stdClass();
        }

        return json_decode(json_encode((object) $array));
    }
}
