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

namespace Modules\Core\Tests\Unit\Casts;

use Modules\Core\Casts\ISO8601DateTime;
use Tests\Fixtures\Event;
use Tests\TestCase;

class SO8601DateTimeTest extends TestCase
{
    public function test_getter_does_not_cast_when_value_is_empty()
    {
        $cast = new ISO8601DateTime;

        $this->assertNull($cast->get(new Event, 'date', null, []));
        $this->assertNull($cast->get(new Event, 'date', '', []));
    }

    public function test_getter_casts_properly_when_value_has_time()
    {
        $cast = new ISO8601DateTime;

        $this->assertSame(
            '2022-01-20 15:00:00',
            $cast->get(new Event, 'date', '2022-01-20 15:00:00', [])->format('Y-m-d H:i:s')
        );
    }

    public function test_setter_does_not_cast_when_value_is_empty()
    {
        $cast = new ISO8601DateTime;

        $this->assertNull($cast->set(new Event, 'date', null, []));
        $this->assertNull($cast->set(new Event, 'date', '', []));
    }

    public function test_setter_casts_iso_8601_value_properly()
    {
        $cast = new ISO8601DateTime;

        $this->assertSame(
            '2018-03-01 23:00:00',
            $cast->set(new Event, 'date', '2018-03-02T00:00:00+01:00', [])
        );

        $this->assertSame(
            '2018-03-01 23:00:00',
            $cast->set(new Event, 'date', '2018-03-02T00:00:00+0100', [])
        );
    }
}
