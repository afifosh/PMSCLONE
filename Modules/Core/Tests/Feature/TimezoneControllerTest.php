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

namespace Modules\Core\Tests\Feature;

use Tests\TestCase;

class TimezoneControllerTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_timezones_endpoints()
    {
        $this->getJson('/api/timezones')->assertUnauthorized();
    }

    public function test_timezones_can_be_retrieved()
    {
        $this->signIn();

        $this->getJson('/api/timezones')->assertOk();
    }
}
