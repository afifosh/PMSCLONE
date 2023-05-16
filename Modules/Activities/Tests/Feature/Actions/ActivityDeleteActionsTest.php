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

class ActivityDeleteActionsTest extends ResourceTestCase
{
    protected $resourceName = 'activities';

    public function test_super_admin_user_can_run_activity_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $activity = $this->factory()->for($user)->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    public function test_authorized_user_can_run_activity_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('delete any activity')->signIn();

        $this->createUser();
        $activity = $this->factory()->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    public function test_authorized_user_can_run_activity_delete_action_only_on_own_activities()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own activities')->signIn();
        $this->createUser();

        $activityForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('activities', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activityForSignedIn->id],
        ]);

        $this->assertSoftDeleted('activities', ['id' => $activityForSignedIn->id]);
    }

    public function test_unauthorized_user_can_run_activity_delete_action_on_own_activity()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own activities')->signIn();
        $user = $this->createUser();

        $activityForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->for($user)->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('activities', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activityForSignedIn->id],
        ]);

        $this->assertSoftDeleted('activities', ['id' => $activityForSignedIn->id]);
    }

    public function test_super_super_admin_user_can_run_activity_bulk_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $activity = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    public function test_authorized_user_can_run_activity_bulk_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('bulk delete activities')->signIn();

        $user = $this->createUser();
        $activity = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activity->id],
        ])->assertOk();

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    public function test_unauthorized_user_cant_run_activity_bulk_delete_action()
    {
        $this->asRegularUser()->signIn();
        $user = $this->createUser();
        $activity = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$activity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
    }
}
