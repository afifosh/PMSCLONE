<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\PredefinedMailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PredefinedMailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PredefinedMailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name(),
            'subject'   => $this->faker->sentence(),
            'body'      => '<p>' . $this->faker->paragraph() . '</p',
            'is_shared' => true,
            'user_id'   => User::factory(),
        ];
    }

    /**
     * Indicate that the template is personal.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function personal()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_shared' => false,
            ];
        });
    }

    /**
     * Indicate that the template is shared.
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
