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

namespace Modules\Activities\Tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Modules\Activities\Models\Activity;
use Modules\Activities\Support\SyncNextActivity;
use Tests\TestCase;

class NextActivityTest extends TestCase
{
    public function test_resource_has_next_activity()
    {
        $activities = $this->activityFactory()->create();

        foreach (SyncNextActivity::resourcesWithNextActivity() as $resource) {
            $model = $resource::$model::factory()->create();
            $model->activities()->attach($activities);

            $this->invokeSync();
            $this->assertTrue($activities[0]->is($model->fresh()->nextActivity));

            $model->activities()->where('id', $activities[0]->id)->detach();
            $this->invokeSync();
            $this->assertTrue($activities[1]->is($model->fresh()->nextActivity));
        }
    }

    public function test_resource_next_activity_is_cleared_when_has_no_activities()
    {
        $activities = $this->activityFactory()->create();

        foreach (SyncNextActivity::resourcesWithNextActivity() as $resource) {
            $model = $resource::$model::factory()->create();
            $model->activities()->attach($activities);
            $this->invokeSync();
            $model->activities()->detach();

            $this->invokeSync();
            $this->assertNull($model->fresh()->nextActivity);
        }
    }

    public function test_resource_next_activity_is_cleared_when_activities_are_completed()
    {
        foreach (SyncNextActivity::resourcesWithNextActivity() as $resource) {
            $activities = $this->activityFactory()->create();
            $model = $resource::$model::factory()->create();
            $model->activities()->attach($activities[0]);

            $activities[0]->markAsComplete();
            $this->invokeSync();

            $this->assertNull($model->fresh()->nextActivity);
        }
    }

    public function test_resource_record_updated_at_is_not_updated_when_updating_next_activity()
    {
        $activities = $this->activityFactory()->create();

        foreach (SyncNextActivity::resourcesWithNextActivity() as $resource) {
            $model = $resource::$model::factory()->create([
                'updated_at' => $updatedAt = now()->subDays(2),
            ]);

            $model->activities()->attach($activities);

            $this->invokeSync();
            $this->assertTrue($activities[0]->is($model->fresh()->nextActivity));
            $this->assertSame($updatedAt->format('Y-m-d H:i:s'), $model->fresh()->updated_at->format('Y-m-d H:i:s'));

            $model->activities()->where('id', $activities[0]->id)->detach();
            $this->invokeSync();
            $this->assertSame($updatedAt->format('Y-m-d H:i:s'), $model->fresh()->updated_at->format('Y-m-d H:i:s'));
        }
    }

    protected function invokeSync()
    {
        (new SyncNextActivity())();
    }

    protected function activityFactory()
    {
        $now = now();

        return Activity::factory()->count(2)->state(new Sequence(
            ['due_date' => $now->addWeeks(1)->format('Y-m-d'), 'due_time' => $now->format('H:i:s')],
            ['due_date' => $now->addWeeks(2)->format('Y-m-d'), 'due_time' => $now->format('H:i:s')]
        ));
    }
}
