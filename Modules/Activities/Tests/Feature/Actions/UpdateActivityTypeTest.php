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

namespace Modules\Activities\Tests\Feature\Actions;

use Modules\Activities\Actions\UpdateActivityType;
use Modules\Activities\Models\Activity;
use Modules\Activities\Models\ActivityType;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Tests\ResourceTestCase;

class UpdateActivityTypeTest extends ResourceTestCase
{
    protected $action;

    protected $resourceName = 'activities';

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateActivityType;
    }

    protected function tearDown(): void
    {
        unset($this->action);
        parent::tearDown();
    }

    public function test_super_admin_user_can_run_update_activity_type_action()
    {
        $this->signIn();
        $this->createUser();
        $activity = $this->factory()->create();
        $type = ActivityType::factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'activity_type_id' => $type->id,
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertEquals($type->id, $activity->fresh()->activity_type_id);
    }

    public function test_authorized_user_can_run_update_activity_type_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('edit all activities')->signIn();

        $this->createUser();
        $activity = Activity::factory()->create();
        $type = ActivityType::factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'activity_type_id' => $type->id,
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertEquals($type->id, $activity->fresh()->activity_type_id);
    }

    public function test_unauthorized_user_can_run_update_activity_type_action_on_own_activity()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('edit own activities')->signIn();
        $this->createUser();

        $type = ActivityType::factory()->create();
        $activityForSignedIn = $this->factory()->for($signedInUser)->create();
        $otherActivity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'activity_type_id' => $type->id,
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->postJson($this->actionEndpoint($this->action), [
            'activity_type_id' => $type->id,
            'ids' => [$activityForSignedIn->id],
        ]);

        $this->assertEquals($type->id, $activityForSignedIn->fresh()->activity_type_id);
    }

    public function test_update_activity_type_action_requires_type()
    {
        $this->signIn();
        $this->createUser();
        $activity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'activity_type_id' => '',
            'ids' => [$activity->id],
        ])->assertJsonValidationErrors('activity_type_id');
    }
}
