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

namespace Modules\Contacts\Tests\Feature;

use Modules\Activities\Models\Activity;
use Modules\Calls\Models\Call;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Phone;
use Modules\Contacts\Models\Source;
use Modules\Core\Database\Seeders\CountriesSeeder;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Models\Country;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Tests\ResourceTestCase;
use Modules\Deals\Models\Deal;
use Modules\Notes\Models\Note;

class CompanyResourceTest extends ResourceTestCase
{
    protected $resourceName = 'companies';

    public function test_user_can_create_resource_record()
    {
        $this->signIn();
        $this->seed(CountriesSeeder::class);
        $user = $this->createUser();
        $source = Source::factory()->create();
        $contact = Contact::factory()->create();
        $deal = Deal::factory()->create();

        $response = $this->postJson($this->createEndpoint(), [
            'name' => 'KONKORD DIGITAL',
            'domain' => 'concordcrm.com',
            'email' => 'concordcrm@example.com',
            'phones' => [
                ['number' => '+123654-88-885', 'type' => 'work'],
                ['number' => '+123654-77-885', 'type' => 'mobile'],
                ['number' => '+123654-66-885', 'type' => 'other'],
                ['number' => '', 'type' => 'other'],
            ],
            'source_id' => $source->id,
            'user_id' => $user->id,
            'deals' => [$deal->id],
            'contacts' => [$contact->id],
        ])
            ->assertCreated();

        $this->assertResourceJsonStructure($response);

        $response->assertJsonCount(1, 'contacts')
            ->assertJsonCount(1, 'deals')
            ->assertJson([
                'contacts' => [['id' => $contact->id]],
                'deals' => [['id' => $deal->id]],
                'name' => 'KONKORD DIGITAL',
                'domain' => 'concordcrm.com',
                'email' => 'concordcrm@example.com',
                'phones' => [
                    ['number' => '+123654-88-885', 'type' => 'work'],
                    ['number' => '+123654-77-885', 'type' => 'mobile'],
                    ['number' => '+123654-66-885', 'type' => 'other'],
                ],
                'source_id' => $source->id,
                'user_id' => $user->id,
                'was_recently_created' => true,
                'path' => '/companies/1',
                'display_name' => 'KONKORD DIGITAL',
                'contacts_count' => 1,
                'deals_count' => 1,
            ]);
    }

    public function test_user_can_update_resource_record()
    {
        $this->seed(CountriesSeeder::class);
        $user = $this->signIn();
        $record = $this->factory()->has(Phone::factory()->count(2), 'phones')
            ->has(Contact::factory())->create();
        $source = Source::factory()->create();
        $contact = Contact::factory()->create();
        $deal = Deal::factory()->create();

        $response = $this->putJson($this->updateEndpoint($record), [
            'name' => 'KONKORD DIGITAL',
            'domain' => 'concordcrm.com',
            'email' => 'concordcrm@example.com',
            'phones' => [
                ['id' => $record->phones[0]->id, 'number' => $record->phones[0]->number, '_delete' => true],
                ['id' => $record->phones[1]->id, 'number' => '+136547-96636', 'type' => 'work'],
                ['number' => '+123654-88-885', 'type' => 'work'],
                ['number' => '+123654-77-885', 'type' => 'mobile'],
                ['number' => '+123654-66-885', 'type' => 'other'],
                ['number' => '', 'type' => 'other'],
            ],
            'source_id' => $source->id,
            'source' => ['id' => $source->id],
            'user_id' => $user->id,
            'user' => ['id' => $user->id],
            'deals' => [$deal->id],
            'contacts' => [$contact->id],
        ])
            ->assertOk();

        $this->assertResourceJsonStructure($response);

        $response->assertJsonCount(count($this->resource()->resolveActions(app(ResourceRequest::class))), 'actions')
            ->assertJsonCount(4, 'phones')
            ->assertJsonCount(1, 'contacts')
            ->assertJsonCount(1, 'deals')
            ->assertJson([
                'contacts' => [['id' => $contact->id]],
                'deals' => [['id' => $deal->id]],
                'name' => 'KONKORD DIGITAL',
                'domain' => 'concordcrm.com',
                'email' => 'concordcrm@example.com',
                'source_id' => $source->id,
                'source' => ['id' => $source->id],
                'user_id' => $user->id,
                'user' => ['id' => $user->id],
                'path' => '/companies/1',
                'display_name' => 'KONKORD DIGITAL',
                'contacts_count' => 1,
                'deals_count' => 1,
            ]);
    }

    public function test_unauthorized_user_cannot_update_resource_record()
    {
        $this->asRegularUser()->signIn();
        $record = $this->factory()->create();

        $this->putJson($this->updateEndpoint($record), [
            'name' => 'KONKORD DIGITAL',
        ])->assertForbidden();
    }

    public function test_authorized_user_can_update_own_resource_record()
    {
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()->withPermissionsTo('edit own companies')->signIn();
        $record = $this->factory()->for($user)->create();

        $this->putJson($this->updateEndpoint($record), [
            'name' => 'KONKORD DIGITAL',
        ])->assertOk();
    }

    public function test_authorized_user_can_update_resource_record()
    {
        $this->seed(PermissionsSeeder::class);
        $this->asRegularUser()->withPermissionsTo('edit all companies')->signIn();
        $record = $this->factory()->create();

        $this->putJson($this->updateEndpoint($record), [
            'name' => 'KONKORD DIGITAL',
        ])->assertOk();
    }

    public function test_it_can_retrieve_resource_records()
    {
        $this->signIn();

        $this->factory()->count(5)->create();

        $this->getJson($this->indexEndpoint())->assertJsonCount(5, 'data');
    }

    public function test_it_can_retrieve_resource_record()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->getJson($this->showEndpoint($record))->assertOk();
    }

    public function test_it_can_globally_search_companies()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->getJson("/api/search?q={$record->name}")
            ->assertJsonCount(1, '0.data')
            ->assertJsonPath('0.data.0.id', $record->id)
            ->assertJsonPath('0.data.0.path', $record->path)
            ->assertJsonPath('0.data.0.display_name', $record->display_name);
    }

    public function test_an_unauthorized_user_can_global_search_only_own_records()
    {
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()->withPermissionsTo('view own companies')->signIn();
        $user1 = $this->createUser();

        $this->factory()->for($user1)->create(['name' => 'KONKORD']);
        $record = $this->factory()->for($user)->create(['name' => 'KONKORD DIGITAL']);

        $this->getJson('/api/search?q=KONKORD')
            ->assertJsonCount(1, '0.data')
            ->assertJsonPath('0.data.0.id', $record->id)
            ->assertJsonPath('0.data.0.path', $record->path)
            ->assertJsonPath('0.data.0.display_name', $record->display_name);
    }

    public function test_it_can_search_emails_for_companies()
    {
        $this->signIn();

        $record = $this->factory()->create(['email' => 'konkord@example.com']);

        $this->getJson('/api/search/email-address?q=konkord@example.com')
            ->assertJsonCount(1, '0.data')
            ->assertJsonPath('0.data.0.id', $record->id)
            ->assertJsonPath('0.data.0.path', $record->path)
            ->assertJsonPath('0.data.0.address', 'konkord@example.com')
            ->assertJsonPath('0.data.0.resourceName', $this->resourceName)
            ->assertJsonPath('0.data.0.name', $record->display_name);
    }

    public function test_it_can_force_delete_resource_record()
    {
        $this->signIn();

        $record = $this->factory()
            ->has(Contact::factory())
            ->has(Note::factory())
            ->has(Call::factory())
            ->has(Activity::factory())
            ->has(Deal::factory())
            ->create();

        $record->delete();

        $this->deleteJson($this->forceDeleteEndpoint($record))->assertNoContent();
        $this->assertDatabaseCount($this->tableName(), 0);
    }

    public function test_it_can_soft_delete_resource_record()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->deleteJson($this->deleteEndpoint($record))->assertNoContent();
        $this->assertDatabaseCount($this->tableName(), 1);
    }

    public function test_user_can_export_companies()
    {
        $this->performExportTest();
    }

    public function test_user_can_create_resource_record_with_custom_fields()
    {
        $this->signIn();

        $response = $this->postJson($this->createEndpoint(), array_merge([
            'name' => 'KONKORD DIGITAL',
        ], $this->customFieldsPayload()))->assertCreated();

        $this->assertThatResponseHasCustomFieldsValues($response);
    }

    public function test_user_can_update_resource_record_with_custom_fields()
    {
        $this->signIn();
        $record = $this->factory()->create();

        $response = $this->putJson($this->updateEndpoint($record), array_merge([
            'name' => 'KONKORD DIGITAL',
        ], $this->customFieldsPayload()))->assertOk();

        $this->assertThatResponseHasCustomFieldsValues($response);
    }

    public function test_user_can_import_companies()
    {
        $this->seed(CountriesSeeder::class);
        $this->createUser();

        $this->performImportTest();
    }

    public function test_user_can_import_companies_with_custom_fields()
    {
        $this->seed(CountriesSeeder::class);

        $this->createUser();

        $this->performImportWithCustomFieldsTest();
    }

    public function test_it_properly_finds_duplicate_companies_during_import()
    {
        $this->seed(CountriesSeeder::class);
        $this->createUser();

        $this->factory()->create($overrideValues = [
            'street' => 'Street',
            'city' => 'City',
            'postal_code' => 2400,
            'country_id' => Country::inRandomOrder()->first()->id,
        ]);

        $this->performImportWithDuplicateTest($overrideValues);
    }

    public function test_user_properly_finds_duplicate_companies_during_import_via_email()
    {
        $this->seed(CountriesSeeder::class);
        $this->createUser();
        $this->factory()->create(['email' => 'duplicate@example.com', 'street' => null]);

        $this->performImportWithDuplicateTest(['email' => 'duplicate@example.com', 'street' => null]);
    }

    public function test_user_can_load_the_companies_table()
    {
        $this->performTestTableLoad();
    }

    public function test_companies_table_loads_all_fields()
    {
        $this->performTestTableCanLoadWithAllFields();
    }

    public function test_companies_table_can_be_customized()
    {
        $user = $this->signIn();

        $this->postJson($this->tableEndpoint().'/settings', [
            'maxHeight' => '120px',
            'columns' => [
                ['attribute' => 'created_at', 'order' => 2, 'hidden' => false],
                ['attribute' => 'domain', 'order' => 3, 'hidden' => true],
            ],
            'order' => [
                ['attribute' => 'created_at', 'direction' => 'asc'],
                ['attribute' => 'name', 'direction' => 'desc'],
            ],
        ])->assertOk();

        $settings = $this->resource()->resolveTable($this->createRequestForTable($user))->settings();

        $this->assertSame('120px', $settings->maxHeight());
        $this->assertCount(2, $settings->getCustomizedColumns());
        $this->assertCount(2, $settings->getCustomizedOrder());
    }

    public function test_companies_table_has_trashed_actions()
    {
        $this->signIn();

        $table = $this->resource()->resolveTrashedTable($this->createRequestForTable());

        $this->assertCount(2, $table->actionsForTrashedTable());
    }

    protected function assertResourceJsonStructure($response)
    {
        $response->assertJsonStructure([
            'actions', 'calls_count', 'changelog', 'city', 'contacts', 'contacts_count', 'country', 'country_id', 'created_at', 'deals', 'deals_count', 'display_name', 'domain', 'email', 'id', 'industry', 'industry_id', 'media', 'name', 'next_activity_date', 'notes_count', 'owner_assigned_date', 'parent', 'parent_company_id', 'parents', 'phones', 'postal_code', 'source', 'source_id', 'state', 'street', 'timeline_subject_key', 'incomplete_activities_for_user_count', 'unread_emails_for_user_count', 'updated_at', 'path', 'user', 'user_id', 'was_recently_created', 'authorizations' => [
                'create', 'delete', 'update', 'view', 'viewAny',
            ],
        ]);
    }
}
