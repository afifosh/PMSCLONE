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

class CompanyDeleteActionsTest extends ResourceTestCase
{
    protected $resourceName = 'companies';

    public function test_super_admin_user_can_run_company_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $company = $this->factory()->for($user)->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$company->id],
        ])->assertOk();

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_authorized_user_can_run_company_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('delete any company')->signIn();

        $this->createUser();
        $company = $this->factory()->create();
        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$company->id],
        ])->assertOk();

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_authorized_user_can_run_company_delete_action_only_on_own_companies()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own companies')->signIn();
        $this->createUser();

        $companyForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('companies', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$companyForSignedIn->id],
        ]);

        $this->assertSoftDeleted('companies', ['id' => $companyForSignedIn->id]);
    }

    public function test_unauthorized_user_can_run_company_delete_action_on_own_company()
    {
        $this->seed(PermissionsSeeder::class);

        $signedInUser = $this->asRegularUser()->withPermissionsTo('delete own companies')->signIn();
        $user = $this->createUser();

        $companyForSignedIn = $this->factory()->create(['user_id' => $signedInUser->id]);
        $otherActivity = $this->factory()->for($user)->create();

        $action = $this->findAction('delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$otherActivity->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('companies', ['id' => $otherActivity->id]);

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$companyForSignedIn->id],
        ]);

        $this->assertSoftDeleted('companies', ['id' => $companyForSignedIn->id]);
    }

    public function test_super_super_admin_user_can_run_company_bulk_delete_action()
    {
        $this->signIn();
        $user = $this->createUser();
        $company = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$company->id],
        ])->assertOk();

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_authorized_user_can_run_company_bulk_delete_action()
    {
        $this->seed(PermissionsSeeder::class);

        $this->asRegularUser()->withPermissionsTo('bulk delete companies')->signIn();

        $user = $this->createUser();
        $company = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$company->id],
        ])->assertOk();

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_unauthorized_user_cant_run_company_bulk_delete_action()
    {
        $this->asRegularUser()->signIn();
        $user = $this->createUser();
        $company = $this->factory()->for($user)->create();
        $action = $this->findAction('bulk-delete');

        $this->postJson($this->actionEndpoint($action), [
            'ids' => [$company->id],
        ])->assertJson(['error' => __('users::user.not_authorized')]);

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }
}
