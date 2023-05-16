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

use Modules\Activities\Actions\MarkActivityAsComplete;
use Modules\Activities\Models\Activity;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Tests\ResourceTestCase;

class MarkActivityAsCompleteTest extends ResourceTestCase
{
    protected $action;

    protected $resourceName = 'activities';

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new MarkActivityAsComplete;
    }

    protected function tearDown(): void
    {
        unset($this->action);
        parent::tearDown();
    }

    public function test_super_admin_user_can_run_mark_activity_as_complete_action()
    {
        $this->signIn();
        $this->createUser();
        $activity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertTrue((bool) $activity->fresh()->isCompleted);
    }

    public function test_authorized_user_can_run_mark_activity_as_complete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('edit all activities')->signIn();

        $this->createUser();
        $activity = Activity::factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertTrue((bool) $activity->fresh()->isCompleted);
    }

    public function test_unauthorized_user_can_run_mark_activity_as_complete_action_on_own_activity()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('edit own activities')->signIn();
        $this->createUser();

        $activityForSignedIn = $this->factory()->for($signedInUser)->create();
        $otherActivity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [$activityForSignedIn->id],
        ]);

        $this->assertTrue((bool) $activityForSignedIn->fresh()->isCompleted);
    }
}
