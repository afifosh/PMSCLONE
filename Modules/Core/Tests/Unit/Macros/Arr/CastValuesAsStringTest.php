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

namespace Modules\Core\Tests\Unit\Macros\Arr;

use Illuminate\Support\Arr;
use Tests\TestCase;

class CastValuesAsStringTest extends TestCase
{
    public function test_it_casts_values_as_string()
    {
        $casts = Arr::valuesAsString([1, 2, 3]);

        $this->assertSame('1', $casts[0]);
        $this->assertSame('2', $casts[1]);
        $this->assertSame('3', $casts[2]);
    }
}
