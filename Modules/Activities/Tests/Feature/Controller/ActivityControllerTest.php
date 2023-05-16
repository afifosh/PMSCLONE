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

namespace Modules\Activities\Tests\Feature\Controller;

use Modules\Activities\Models\Activity;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_the_activity_end_points()
    {
        $activity = Activity::factory()->create();

        $this->getJson('/api/activities/'.$activity->id.'/ics')->assertUnauthorized();
    }

    public function test_activity_ics_file_can_be_downloaded()
    {
        $this->signIn();

        $activity = Activity::factory()->create();

        $this->getJson('/api/activities/'.$activity->getKey().'/ics')
            ->assertHeader('Content-Type', 'text/calendar; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename='.$activity->icsFilename().'.ics');
    }
}
