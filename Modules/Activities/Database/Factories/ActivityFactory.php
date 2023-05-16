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

namespace Modules\Activities\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Activities\Models\Activity;
use Modules\Activities\Models\ActivityType;
use Modules\Users\Models\User;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$dueDate, $endDate] = $this->getDates();

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'note' => $this->faker->text(),
            'due_date' => $dueDate->format('Y-m-d'),
            'due_time' => $dueDate->format('H:i:s'),
            'end_date' => $endDate->format('Y-m-d'),
            'end_time' => $endDate->format('H:i:s'),
            'reminder_minutes_before' => 30,
            'user_id' => User::factory(),
            'activity_type_id' => ActivityType::factory(),
            'created_at' => $this->faker->dateTimeBetween('-7 days')->format('Y-m-d H:i:s'),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the activity has no reminder.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function noReminder()
    {
        return $this->state(function (array $attributes) {
            return [
                'reminder_minutes_before' => null,
            ];
        });
    }

    /**
     * Indicate that the activity is all day.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function allDay()
    {
        return $this->state(function (array $attributes) {
            return [
                'due_time' => null,
                'end_time' => null,
            ];
        });
    }

    /**
     * Indicate that the activity is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'completed_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the activity is incomplete.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'completed_at' => null,
            ];
        });
    }

    /**
     * Indicate that the activity is reminded.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function reminded()
    {
        return $this->state(function (array $attributes) {
            return [
                'reminded_at' => now(),
            ];
        });
    }

    /**
     * Get the dates for the activity
     *
     * @return array
     */
    protected function getDates()
    {
        $dueDate = $this->faker->dateTimeBetween('now', '+6 weeks');
        // Round to nearest 15
        $roundedDueSeconds = round($dueDate->getTimestamp() / (15 * 60)) * (15 * 60);
        $dueDate->setTime($dueDate->format('H'), date('i', $roundedDueSeconds), 0);

        $endDate = clone $dueDate;
        // Add one or zero days to end date and the add 30 minutes
        $endDate->add(new \DateInterval('P'.rand(0, 1).'D'));
        $endDate->add(new \DateInterval('PT30M'));

        return [$dueDate, $endDate];
    }
}
