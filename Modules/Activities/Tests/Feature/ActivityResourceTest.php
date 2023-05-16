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

namespace Modules\Activities\Tests\Feature;

use Modules\Activities\Models\ActivityType;
use Modules\Contacts\Models\Company;
use Modules\Contacts\Models\Contact;
use Modules\Core\Database\Seeders\PermissionsSeeder;
use Modules\Core\Date\Carbon;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Tests\ResourceTestCase;
use Modules\Deals\Models\Deal;
use Modules\Users\Models\Team;
use Modules\Users\Models\User;

class ActivityResourceTest extends ResourceTestCase
{
    protected $samplePayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->samplePayload = [
            'title' => 'Activity Title',
            'due_date' => '2021-12-14 15:00:00',
            'end_date' => '2021-12-15 17:00:00',
            'activity_type_id' => ActivityType::factory()->create()->id,
            'user_id' => $this->createUser()->id,
        ];
    }

    protected function tearDown(): void
    {
        unset($this->samplePayload);
        parent::tearDown();
    }

    protected $resourceName = 'activities';

    public function test_user_can_create_resource_record()
    {
        $this->signIn();
        $user = $this->createUser();
        $type = ActivityType::factory()->create();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create();
        $deal = Deal::factory()->create();

        $response = $this->postJson($this->createEndpoint(), [
            'title' => 'Activity Title',
            'description' => 'Description',
            'note' => 'Note',
            'due_date' => $dueDate = Carbon::parse()->addMonth()->format('Y-m-d'),
            'due_time' => $dueTime = Carbon::parse()->addMonth()->format('H:i'),
            'end_date' => $endDate = Carbon::parse()->addMonth()->format('Y-m-d'),
            'end_time' => $endTime = Carbon::parse()->addMonth()->format('H:i'),
            'reminder_minutes_before' => 60,
            'activity_type_id' => $type->id,
            'user_id' => $user->id,
            'companies' => [$company->id],
            'contacts' => [$contact->id],
            'deals' => [$deal->id],
            'guests' => ['users' => [$user->id], 'contacts' => [$contact->id]],
        ])
            ->assertCreated();

        $this->assertResourceJsonStructure($response);

        $response->assertJsonCount(1, 'companies')
            ->assertJsonCount(1, 'contacts')
            ->assertJsonCount(1, 'deals')
            ->assertJsonCount(2, 'guests')
            ->assertJson([
                'companies' => [
                    ['id' => $company->id],
                ],
                'contacts' => [
                    ['id' => $contact->id],
                ],
                'deals' => [
                    ['id' => $deal->id],
                ],
                'is_completed' => false,
                'is_due' => false,
                'is_reminded' => false,
                'title' => 'Activity Title',
                'description' => 'Description',
                'note' => 'Note',
                'due_date' => [
                    'date' => $dueDate,
                    'time' => $dueTime,
                ],
                'end_date' => [
                    'date' => $endDate,
                    'time' => $endTime,
                ],
                'reminder_minutes_before' => (string) 60,
                'activity_type_id' => (string) $type->id,
                'user_id' => (string) $user->id,
                'was_recently_created' => true,
                'path' => '/activities/1',
                'display_name' => 'Activity Title',
            ]);
    }

    public function test_activity_due_date_and_end_date_can_be_provided_as_full_date()
    {
        $this->signIn();

        $this->postJson($this->createEndpoint(), $this->samplePayload)
            ->assertCreated()->assertJson([
                'due_date' => [
                    'date' => '2021-12-14',
                    'time' => '15:00',
                ],
                'end_date' => [
                    'date' => '2021-12-15',
                    'time' => '17:00',
                ],
            ]);

        $this->postJson($this->createEndpoint(), array_merge($this->samplePayload, [
            'due_date' => '2021-12-13',
            'due_time' => '14:00',
        ]))->assertCreated()->assertJson([
            'due_date' => [
                'date' => '2021-12-13',
                'time' => '14:00',
            ],
            'end_date' => [
                'date' => '2021-12-15',
                'time' => '17:00',
            ],
        ]);

        $this->postJson($this->createEndpoint(), array_merge($this->samplePayload, [
            'end_date' => '2021-12-14',
            'end_time' => '15:00',
        ]))->assertCreated()->assertJson([
            'due_date' => [
                'date' => '2021-12-14',
                'time' => '15:00',
            ],
            'end_date' => [
                'date' => '2021-12-14',
                'time' => '15:00',
            ],
        ]);
    }

    public function test_it_uses_the_due_date_when_end_date_is_not_present()
    {
        $payload = $this->samplePayload;
        unset($payload['end_date']);

        $this->signIn();

        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_date' => '2021-12-14',
            'due_time' => '15:00',
        ]))->assertCreated()->assertJson([
            'end_date' => [
                'date' => '2021-12-14',
                'time' => null,
            ],
        ]);
    }

    public function test_prioritize_the_time_from_the_provided_attribute_instead_of_the_full_date()
    {
        $this->signIn();

        $this->postJson($this->createEndpoint(), array_merge($this->samplePayload, [
            'due_date' => '2021-12-14 14:25:00',
            'due_time' => '15:00',
            'end_date' => '2021-12-14 16:00:00',
            'end_time' => '16:30:00',
        ]))->assertCreated()->assertJson([
            'end_date' => [
                'date' => '2021-12-14',
                'time' => '15:00',
            ],
            'end_date' => [
                'date' => '2021-12-14',
                'time' => '16:30',
            ],
        ]);
    }

    public function test_user_can_create_full_day_activity_with_same_ending_day()
    {
        $this->signIn();

        $this->postJson($this->createEndpoint(), array_merge($this->samplePayload, [
            'due_date' => '2021-12-14',
            'end_date' => '2021-12-14',
        ]))->assertCreated()->assertJson([
            'end_date' => [
                'date' => '2021-12-14',
                'time' => null,
            ],
            'end_date' => [
                'date' => '2021-12-14',
                'time' => null,
            ],
        ]);
    }

    public function test_user_can_create_full_day_activity_with_different_ending_day()
    {
        $this->signIn();

        $this->postJson($this->createEndpoint(), array_merge($this->samplePayload, [
            'due_date' => '2021-12-14',
            'end_date' => '2021-12-17',
        ]))->assertCreated()->assertJson([
            'end_date' => [
                'date' => '2021-12-14',
                'time' => null,
            ],
            'end_date' => [
                'date' => '2021-12-17',
                'time' => null,
            ],
        ]);
    }

    public function test_activity_due_and_end_date_are_properly_validated()
    {
        $this->signIn();
        $payload = $this->samplePayload;
        unset($payload['due_date'],$payload['end_date']);

        // Does not fails on creation when the dates are equal
        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_date' => '2021-12-14 15:00:00',
            'end_date' => '2021-12-14 15:00:00',
        ]))->assertCreated();

        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_date' => '2021-12-14',
            'end_date' => '2021-12-14',
        ]))->assertCreated();

        // End date is less then due date
        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_date' => '2021-12-14 15:00:00',
            'end_date' => '2021-12-13 14:00:00',
        ]))->assertJsonValidationErrors(['end_date' => __('activities::activity.validation.end_date.less_than_due')]);

        // Time is required when the date is in future
        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_date' => '2021-12-14 15:00:00',
            'end_date' => '2021-12-16',
        ]))->assertJsonValidationErrors(['end_date' => __('activities::activity.validation.end_time.required_when_end_date_is_in_future')]);

        // Requires due_date when time is provided
        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'due_time' => '15:00:00',
        ]))->assertJsonValidationErrorFor('due_date');

        // Required end_date when time is provided
        $this->postJson($this->createEndpoint(), array_merge($payload, [
            'end_time' => '15:00:00',
        ]))->assertJsonValidationErrorFor('end_date');
    }

    public function test_it_uses_the_default_type_when_is_activity_type_id_is_not_present()
    {
        $this->signIn();
        $payload = $this->samplePayload;
        unset($payload['activity_type_id']);
        ActivityType::setDefault(ActivityType::first()->id);

        $this->postJson($this->createEndpoint(), $payload)->assertJson([
            'activity_type_id' => ActivityType::first()->id,
        ]);
    }

    public function test_it_requires_type_when_present()
    {
        $this->signIn();
        $payload = $this->samplePayload;
        $activity = $this->factory()->create();
        $payload['activity_type_id'] = null;

        $this->putJson($this->updateEndpoint($activity), $payload)
            ->assertJsonValidationErrors(['activity_type_id' => __('validation.filled', [
                'attribute' => 'Activity Type',
            ])]);

        $this->postJson($this->createEndpoint(), $payload)
            ->assertJsonValidationErrors(['activity_type_id' => __('validation.filled', [
                'attribute' => 'Activity Type',
            ])]);
    }

    public function test_on_creation_it_requires_type_when_there_is_no_default_type()
    {
        $this->signIn();
        $payload = $this->samplePayload;
        unset($payload['activity_type_id']);

        $this->postJson($this->createEndpoint(), $payload)->assertJsonValidationErrorFor('activity_type_id');
    }

    public function test_user_can_update_resource_record()
    {
        $user = $this->signIn();
        $type = ActivityType::factory()->create();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create();
        $deal = Deal::factory()->create();
        $record = $this->factory()->has(Company::factory())->create();

        $response = $this->putJson($this->updateEndpoint($record), [
            'title' => 'Activity Title',
            'description' => 'Description',
            'note' => 'Note',
            'due_date' => $dueDate = Carbon::parse()->addMonth()->format('Y-m-d'),
            'due_time' => $dueTime = Carbon::parse()->addMonth()->format('H:i'),
            'end_date' => $endDate = Carbon::parse()->addMonth()->format('Y-m-d'),
            'end_time' => $endTime = Carbon::parse()->addMonth()->format('H:i'),
            'reminder_minutes_before' => 60,
            'activity_type_id' => $type->id,
            'user_id' => $user->id,
            'companies' => [$company->id],
            'contacts' => [$contact->id],
            'deals' => [$deal->id],
            'guests' => ['users' => [$user->id], 'contacts' => [$contact->id]],
        ])
            ->assertOk();

        $this->assertResourceJsonStructure($response);

        $response->assertJsonCount(count($this->resource()->resolveActions(app(ResourceRequest::class))), 'actions')
            ->assertJsonCount(1, 'companies')
            ->assertJsonCount(1, 'contacts')
            ->assertJsonCount(1, 'deals')
            ->assertJsonCount(2, 'guests')
            ->assertJson([
                'companies' => [
                    ['id' => $company->id],
                ],
                'contacts' => [
                    ['id' => $contact->id],
                ],
                'deals' => [
                    ['id' => $deal->id],
                ],
                'is_completed' => false,
                'is_due' => false,
                'is_reminded' => false,
                'title' => 'Activity Title',
                'description' => 'Description',
                'note' => 'Note',
                'due_date' => [
                    'date' => $dueDate,
                    'time' => $dueTime,
                ],
                'end_date' => [
                    'date' => $endDate,
                    'time' => $endTime,
                ],
                'reminder_minutes_before' => (string) 60,
                'activity_type_id' => (string) $type->id,
                'user_id' => (string) $user->id,
                'path' => '/activities/1',
                'display_name' => 'Activity Title',
            ]);
    }

    public function test_unauthorized_user_cannot_update_resource_record()
    {
        $this->asRegularUser()->signIn();
        $record = $this->factory()->create();

        $this->putJson($this->updateEndpoint($record), $this->factory()->make()->toArray())
            ->assertForbidden();
    }

    public function test_authorized_user_can_update_own_resource_record()
    {
        $this->seed(PermissionsSeeder::class);

        $user = $this->asRegularUser()->withPermissionsTo('edit own activities')->signIn();
        $record = $this->factory()->for($user)->create();

        $this->putJson($this->updateEndpoint($record), $this->factory()->make()->toArray())->assertOk();
    }

    public function test_authorized_user_can_update_resource_record()
    {
        $this->seed(PermissionsSeeder::class);

        $user = $this->asRegularUser()->withPermissionsTo('edit all activities')->signIn();
        $record = $this->factory()->for($user)->create();

        $this->putJson($this->updateEndpoint($record), $this->factory()->make()->toArray())->assertOk();
    }

    public function test_user_can_retrieve_resource_records()
    {
        $this->signIn();

        $this->factory()->count(5)->create();

        $this->getJson($this->indexEndpoint())->assertJsonCount(5, 'data');
    }

    public function test_user_can_retrieve_resource_record()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->getJson($this->showEndpoint($record))->assertOk();
    }

    public function test_user_can_globally_search_activities()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->getJson("/api/search?q={$record->title}")
            ->assertJsonCount(1, '0.data')
            ->assertJsonPath('0.data.0.id', $record->id)
            ->assertJsonPath('0.data.0.path', $record->path)
            ->assertJsonPath('0.data.0.display_name', $record->display_name);
    }

    public function test_an_unauthorized_user_can_global_search_only_own_records()
    {
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()->withPermissionsTo('view own activities')->signIn();
        $user1 = $this->createUser();

        $this->factory()->for($user1)->create(['title' => 'KONKORD DIGITAL']);
        $record = $this->factory()->for($user)->create(['title' => 'KONKORD ONLINE']);

        $this->getJson('/api/search?q=KONKORD')
            ->assertJsonCount(1, '0.data')
            ->assertJsonPath('0.data.0.id', $record->id)
            ->assertJsonPath('0.data.0.path', $record->path)
            ->assertJsonPath('0.data.0.display_name', $record->display_name);
    }

    public function test_user_can_force_delete_resource_record()
    {
        $user = $this->signIn();

        $record = $this->factory()
            ->has(Contact::factory())
            ->has(Company::factory())
            ->has(Deal::factory())
            ->create();

        $guest = $user->guests()->create([]);
        $guest->activities()->attach($record);

        $record->delete();

        $this->deleteJson($this->forceDeleteEndpoint($record))->assertNoContent();
        $this->assertDatabaseCount($this->tableName(), 0);
    }

    public function test_user_can_soft_delete_resource_record()
    {
        $this->signIn();

        $record = $this->factory()->create();

        $this->deleteJson($this->deleteEndpoint($record))->assertNoContent();
        $this->assertDatabaseCount($this->tableName(), 1);
    }

    public function test_user_can_export_activities()
    {
        $this->performExportTest();
    }

    public function test_user_can_import_activities()
    {
        $this->createUser();

        $this->performImportTest();
    }

    public function test_user_can_load_the_activities_table()
    {
        $this->performTestTableLoad();
    }

    public function test_activities_table_loads_all_fields()
    {
        $this->performTestTableCanLoadWithAllFields();
    }

    public function test_activities_table_can_be_customized()
    {
        $user = $this->signIn();

        $this->postJson($this->tableEndpoint().'/settings', [
            'maxHeight' => '120px',
            'columns' => [
                ['attribute' => 'created_at', 'order' => 2, 'hidden' => false],
                ['attribute' => 'title', 'order' => 3, 'hidden' => false],
            ],
            'order' => [
                ['attribute' => 'created_at', 'direction' => 'asc'],
                ['attribute' => 'title', 'direction' => 'desc'],
            ],
        ])->assertOk();

        $settings = $this->resource()->resolveTable($this->createRequestForTable($user))->settings();

        $this->assertSame('120px', $settings->maxHeight());
        $this->assertCount(2, $settings->getCustomizedColumns());
        $this->assertCount(2, $settings->getCustomizedOrder());
    }

    public function test_activities_table_has_trashed_actions()
    {
        $this->signIn();

        $table = $this->resource()->resolveTrashedTable($this->createRequestForTable());

        $this->assertCount(2, $table->actionsForTrashedTable());
    }

    public function test_user_can_view_attends_and_owned_including_team_activities()
    {
        // Ticket #461
        $this->seed(PermissionsSeeder::class);
        $user = $this->asRegularUser()
            ->withPermissionsTo(['view attends and owned activities', 'view team activities'])
            ->createUser();

        $teamUser = User::factory()->has(Team::factory()->for($user, 'manager'))->create();

        $teamActivity = $this->factory()->for($teamUser)->create();
        $guestActivity = $this->factory()->for($user)->create();
        $attendsActivity = $this->factory()->create();
        $guest = $user->guests()->create([]);
        $guest->activities()->attach($attendsActivity);

        $this->signIn($user);
        $this->getJson($this->showEndpoint($teamActivity))->assertOk();
        $this->getJson($this->showEndpoint($guestActivity))->assertOk();
        $this->getJson($this->showEndpoint($attendsActivity))->assertOk();
    }

    public function test_user_can_retrieve_activities_related_to_associations_authorized_to_view()
    {
        $this->seed(PermissionsSeeder::class);

        $user = $this->asRegularUser()->withPermissionsTo('view own contacts')->signIn();
        $activity = $this->factory()->has(Contact::factory()->for($user))->create();
        $contact = $activity->contacts[0];

        $this->getJson("/api/activities/$activity->id?via_resource=contacts&via_resource_id=$contact->id")->assertOk();
    }

    protected function assertResourceJsonStructure($response)
    {
        $response->assertJsonStructure([
            'actions', 'activity_type_id', 'associations', 'comments_count', 'companies', 'completed_at', 'contacts', 'created_at', 'created_by', 'creator', 'deals', 'description', 'display_name', 'due_date', 'end_date', 'guests', 'id', 'is_completed', 'is_due', 'is_reminded', 'media', 'note', 'owner_assigned_date', 'reminded_at', 'reminder_minutes_before', 'timeline_component', 'timeline_key', 'timeline_relation', 'title', 'type', 'updated_at', 'path', 'user', 'user_id', 'was_recently_created', 'authorizations' => [
                'create', 'delete', 'update', 'view', 'viewAny',
            ],
        ]);
    }
}
