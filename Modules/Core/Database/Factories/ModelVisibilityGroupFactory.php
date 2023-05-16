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

namespace Modules\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\ModelVisibilityGroup;

class ModelVisibilityGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModelVisibilityGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'all',
        ];
    }

    /**
     * Indicate that the visibility group is teams related.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function teams()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'teams',
            ];
        });
    }

    /**
     * Indicate that the visibility group is users related.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function users()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'users',
            ];
        });
    }
}
