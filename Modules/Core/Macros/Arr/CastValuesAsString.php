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

class CastValuesAsString
{
    /**
     * Cast the provided array values as string
     *
     * @param  array  $array
     * @return array
     */
    public function __invoke($array)
    {
        return array_map(fn ($value) => (string) $value, $array);
    }
}
