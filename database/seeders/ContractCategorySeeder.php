<?php

namespace Database\Seeders;

use App\Models\ContractCategory;
use Illuminate\Database\Seeder;

class ContractCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $contractCategoryNames = [
        //     'CapEx',
        //     'OpEx'
        // ];

        $contractCategoryNames = [
            'CAPEX (Capital Expenditures)',
            'OPEX (Operating Expenses)',
            'General',
            'Overhead Cost'
        ];


        foreach ($contractCategoryNames as $index => $name) {
            $contractCategoryData = [
                'name' => $name
            ];

            ContractCategory::create($contractCategoryData);
        }
    }
}
