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

use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Tests\ResourceTestCase;
use Modules\Users\Actions\AssignOwnerAction;

class ActivityAssignOwnerTest extends ResourceTestCase
{
    protected $action;

    protected $resourceName = 'activities';

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AssignOwnerAction;
    }

    protected function tearDown(): void
    {
        unset($this->action);
        parent::tearDown();
    }

    public function test_super_admin_user_can_run_activity_assign_owner_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $activity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertEquals($user->id, $activity->fresh()->user_id);
    }

    public function test_authorized_user_can_run_activity_assign_owner_action()
    {
        $this->seed(PermissionsSeeder::class);
        $this->asRegularUser()->withPermissionsTo('edit all activities')->signIn();

        $user = $this->createUser();
        $activity = $this->factory()->for($user)->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertEquals($user->id, $activity->fresh()->user_id);
    }

    public function test_unauthorized_user_can_run_activity_assign_owner_action_on_own_activity()
    {
        $this->seed(PermissionsSeeder::class);
        $signedInUser = $this->asRegularUser()->withPermissionsTo('edit own activities')->signIn();
        $user = $this->createUser();

        $activityForSignedIn = $this->factory()->for($signedInUser)->create();
        $otherActivity = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$activityForSignedIn->id],
        ]);

        $this->assertEquals($user->id, $activityForSignedIn->fresh()->user_id);
    }

    public function test_activity_assign_owner_action_requires_owner()
    {
        $this->signIn();

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [],
        ])->assertJsonValidationErrors(['user_id']);
    }
}
