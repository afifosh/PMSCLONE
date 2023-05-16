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
use Modules\Core\Models\Filter;

class FilterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Filter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => 'users',
            'name' => 'Filter Name',
            'is_shared' => false,

            'rules' => [
                'condition' => 'and',
                'children' => [[
                    'type' => 'rule',
                    'query' => [
                        'type' => 'text',
                        'opereator' => 'equal',
                        'rule' => 'test_attribute',
                        'operand' => 'Test',
                        'value' => 'Test',
                    ],
                ]],
            ],
        ];
    }

    /**
     * Indicate that the filter is shared.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function shared()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_shared' => true,
            ];
        });
    }
}
