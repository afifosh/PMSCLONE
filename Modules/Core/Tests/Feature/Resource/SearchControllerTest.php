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

namespace Modules\Core\Tests\Feature\Resource;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Resource\Contact\Contact as ContactResource;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_the_resource_search_endpoints()
    {
        $this->json('GET', '/api/contacts/search')->assertUnauthorized();
    }

    public function test_non_searchable_resource_cannot_be_searched()
    {
        $this->signIn();

        $model = ContactResource::newModel();

        $searchableFields = $model->getSearchableFields();

        $model->setSearchableFields([]);

        $this->json('GET', '/api/contacts/search?q=test')
            ->assertNotFound();

        $model->setSearchableFields($searchableFields);
    }

    public function test_own_criteria_is_applied_on_resource_search()
    {
        $this->seed(PermissionsSeeder::class);

        $user = $this->asRegularUser()->withPermissionsTo('view own contacts')->signIn();

        Contact::factory()->count(2)->state(new Sequence(
            ['first_name' => 'John', 'user_id' => $user->getKey()],
            ['first_name' => 'John', 'user_id' => null]
        ))->create();

        $this->getJson('/api/contacts/search?q=john')
            ->assertJsonCount(1);
    }

    public function test_it_does_not_return_any_results_if_search_query_is_empty()
    {
        $this->signIn();

        Contact::factory()->create();

        $this->json('GET', '/api/contacts/search?q=')
            ->assertJsonCount(0);
    }
}
