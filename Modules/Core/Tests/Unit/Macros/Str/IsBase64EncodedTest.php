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

namespace Modules\Core\Tests\Unit\Macros\Str;

use Illuminate\Support\Str;
use Tests\TestCase;

class IsBase64EncodedTest extends TestCase
{
    public function test_can_check_whether_the_string_is_base64_encoded()
    {
        $this->assertTrue(Str::isBase64Encoded(base64_encode('test')));
        $this->assertTrue(Str::isBase64Encoded('PGEgaHJlZj0iIj5UZXN0PC9hPg=='));
        $this->assertFalse(Str::isBase64Encoded('-test-'));
        $this->assertFalse(Str::isBase64Encoded('Some text'));
    }
}
