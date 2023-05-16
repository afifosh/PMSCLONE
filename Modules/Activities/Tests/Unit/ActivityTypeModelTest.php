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

use Modules\Activities\Models\Activity;
use Modules\Activities\Models\ActivityType;
use Tests\TestCase;

class ActivityTypeModelTest extends TestCase
{
    public function test_activity_type_can_be_primary()
    {
        $type = ActivityType::factory()->primary()->create();

        $this->assertTrue($type->isPrimary());

        $type->flag = null;
        $type->save();

        $this->assertFalse($type->isPrimary());
    }

    public function test_activity_type_can_be_default()
    {
        $type = ActivityType::factory()->primary()->create();

        ActivityType::setDefault($type->id);

        $this->assertEquals($type->id, ActivityType::getDefaultType());
    }

    public function test_type_has_activities()
    {
        $type = ActivityType::factory()->has(Activity::factory()->count(2))->create();

        $this->assertCount(2, $type->activities);
    }

    public function test_primary_type_cannot_be_deleted()
    {
        $type = ActivityType::factory()->primary()->create();

        $this->expectExceptionMessage(__('activities::activity.type.delete_primary_warning'));

        $type->delete();
    }

    public function test_default_type_cannot_be_deleted()
    {
        $type = ActivityType::factory()->create();
        ActivityType::setDefault($type->id);
        $this->expectExceptionMessage(__('activities::activity.type.delete_is_default'));

        $type->delete();
    }

    public function test_type_with_activities_cannot_be_deleted()
    {
        $type = ActivityType::factory()->has(Activity::factory())->create();

        $this->expectExceptionMessage(__('activities::activity.type.delete_usage_warning'));

        $type->delete();
    }

    public function test_due_activities_are_properly_queried()
    {
        $dueDate = now();

        Activity::factory()->create([
            'due_date' => $dueDate->format('Y-m-d'),
            'due_time' => $dueDate->format('H:i'),
        ]);

        $dueDate->addWeek();

        Activity::factory()->create([
            'due_date' => $dueDate->format('Y-m-d'),
            'due_time' => $dueDate->format('H:i'),
        ]);

        $this->assertSame(1, Activity::overdue()->count());
    }
}
