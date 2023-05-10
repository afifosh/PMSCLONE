<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'name' =>  $this->faker->name(),
      'parent_id' => null,
      'program_code' => 'pr-code-' . $this->faker->unique()->randomNumber(5, true),
      'description' => $this->faker->text()
    ];
  }

  /**
   * Set the predefined values for the model.
   *
   * @param array $values
   * @return $this
   */
  public function predefinedValues(array $values)
  {
    return $this->state($values);
  }
    
}
