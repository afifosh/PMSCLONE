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

class ContactDeleteActionsTest extends ResourceTestCase
{
    protected $resourceName = 'contacts';

    public function test_super_admin_user_can_run_contact_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $contact = $this->factory()->for($user)->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_authorized_user_can_run_contact_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('delete any contact')->signIn();

        $this->createUser();
        $contact = $this->factory()->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_authorized_user_can_run_contact_delete_action_only_on_own_contacts()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own contacts')->signIn();
        $this->createUser();

        $contactForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('contacts', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contactForSignedIn->id],
        ]);

        $this->assertSoftDeleted('contacts', ['id' => $contactForSignedIn->id]);
    }

    public function test_unauthorized_user_can_run_contact_delete_action_on_own_contact()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own contacts')->signIn();
        $user = $this->createUser();

        $contactForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->for($user)->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('contacts', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contactForSignedIn->id],
        ]);

        $this->assertSoftDeleted('contacts', ['id' => $contactForSignedIn->id]);
    }

    public function test_super_super_admin_user_can_run_contact_bulk_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $contact = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_authorized_user_can_run_contact_bulk_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('bulk delete contacts')->signIn();

        $user = $this->createUser();
        $contact = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contact->id],
        ])->assertOk();

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_unauthorized_user_cant_run_contact_bulk_delete_action()
    {
        $this->asRegularUser()->signIn();
        $user = $this->createUser();
        $contact = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$contact->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
    }
}
