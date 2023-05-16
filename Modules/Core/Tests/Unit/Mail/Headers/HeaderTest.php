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

namespace Modules\Core\Tests\Unit\Mail\Headers;

use Illuminate\Contracts\Support\Arrayable;
use Modules\Core\Mail\Headers\Header;
use Tests\TestCase;

class HeaderTest extends TestCase
{
    public function test_header_has_name()
    {
        $header = new Header('x-concord-test', 'value');

        $this->assertSame('x-concord-test', $header->getName());
    }

    public function test_header_name_is_aways_in_lowercase()
    {
        $header = new Header('X-Concord-Value', 'value');

        $this->assertSame('x-concord-value', $header->getName());
    }

    public function test_header_has_value()
    {
        $header = new Header('x-concord-test', 'value');

        $this->assertSame('value', $header->getValue());
    }

    public function test_header_is_arrayable()
    {
        $header = new Header('x-concord-test', 'value');

        $this->assertInstanceOf(Arrayable::class, $header);

        $this->assertEquals([
            'name' => 'x-concord-test',
            'value' => 'value',
        ], $header->toArray());
    }
}
