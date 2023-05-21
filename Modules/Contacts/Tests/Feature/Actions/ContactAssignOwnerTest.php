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

namespace Modules\Contacts\Tests\Feature\Actions;

use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Tests\ResourceTestCase;
use Modules\Users\Actions\AssignOwnerAction;

class ContactAssignOwnerTest extends ResourceTestCase
{
    protected $action;

    protected $resourceName = 'contacts';

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

    public function test_super_admin_user_can_run_contact_assign_owner_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $contact = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertEquals($user->id, $contact->fresh()->user_id);
    }

    public function test_authorized_user_can_run_contact_assign_owner_action()
    {
        $this->seed(PermissionsSeeder::class);
        $this->asRegularUser()->withPermissionsTo('edit all contacts')->signIn();

        $user = $this->createUser();
        $contact = $this->factory()->for($user)->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertEquals($user->id, $contact->fresh()->user_id);
    }

    public function test_unauthorized_user_can_run_contact_assign_owner_action_on_own_contact()
    {
        $this->seed(PermissionsSeeder::class);
        $signedInUser = $this->asRegularUser()->withPermissionsTo('edit own contacts')->signIn();
        $user = $this->createUser();

        $contactForSignedIn = $this->factory()->for($signedInUser)->create();
        $otherContact = $this->factory()->create();

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$otherContact->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->postJson($this->actionEndpoint($this->action), [
            'user_id' => $user->id,
            'ids' => [$contactForSignedIn->id],
        ]);

        $this->assertEquals($user->id, $contactForSignedIn->fresh()->user_id);
    }

    public function test_contact_assign_owner_action_requires_owner()
    {
        $this->signIn();

        $this->postJson($this->actionEndpoint($this->action), [
            'ids' => [],
        ])->assertJsonValidationErrors(['user_id']);
    }
}
