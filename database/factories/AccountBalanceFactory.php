<?php

namespace Database\Factories;

use App\Support\LaravelBalance\Models\AccountBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountBalanceFactory extends Factory
{
    protected $model = AccountBalance::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'account_number' => $this->faker->unique()->bankAccountNumber, // Note that your model already generates unique numbers if this field is null
            'currency' => 'SAR',
            'balance' => 0,
            'creator_id' => null,  // Replace with actual data or factory if needed
            'creator_type' => null, // Replace with actual data or factory if needed
        ];
    }
}
