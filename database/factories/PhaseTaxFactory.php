<?php

namespace Database\Factories;

use App\Models\InvoiceConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhaseTax>
 */
class PhaseTaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      $tax = InvoiceConfig::activeTaxes()->inRandomOrder()->first();
        return [
          'contract_phase_id' => null, // set in PhaseFactory
          'tax_id' => $tax->id,
          'amount' => $tax->amount,
          'type' => $tax->type,
          'calculated_amount' => $tax->amount,
          'manual_amount' => 0,
          'category' => $tax->category,
        ];
    }
}
