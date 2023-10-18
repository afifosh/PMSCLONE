<?php


namespace Database\Factories;

use App\Models\Artist;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtistFactory extends Factory
{
    protected $model = Artist::class;

    public function definition()
    {
        return [
            'added_by' => null, // Define how you want to generate the 'added_by' attribute.
            'country_id' => null, // Define how you want to generate the 'country_id' attribute.
            'state_id' => null, // Define how you want to generate the 'state_id' attribute.
            'city_id' => null, // Define how you want to generate the 'city_id' attribute.
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'avatar' => null, // Define how you want to generate the 'avatar' attribute.
            'website' => $this->faker->url,
            'job_title' => $this->faker->jobTitle,
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'address' => $this->faker->address,
            'zip_code' => $this->faker->postcode,
            'language' => 'en',
            'timezone' => $this->faker->timezone,
            'currency' => null, // Define how you want to generate the 'currency' attribute.
            'birth_date' => $this->faker->date,
            'status' => $this->faker->randomElement(['active', 'suspended']),
        ];
    }
}

