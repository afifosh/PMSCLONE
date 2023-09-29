<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $contractTypeNames = [ 'Consulting Agreement',
        'Service Level Agreement',
        'Employment Contract',
        'Non-Disclosure Agreement',
        'Sales Contract',
        'Lease Agreement',
        'Partnership Agreement',
        'Purchase Agreement',
        'Vendor Agreement',
        'Art Competition Agreement',
        'Licensing Agreement',
         'Purchase Order'];

        foreach ( $contractTypeNames as $index => $name) {
            $contractTypeData = [
                'name' => $name
            ];
            
            ContractType::create($contractTypeData);
        }

    }
}
