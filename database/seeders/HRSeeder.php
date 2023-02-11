<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\CompanyDepartment;
use App\Models\CompanyDesignation;
use App\Models\PartnerCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PartnerCompany::factory()->count(20)->create()->each(function($company){
          CompanyDepartment::factory()->count(2)->create(['company_id' => $company, 'head_id' => @Admin::where('id', '>', 3)->inRandomOrder()->first()->id])->each(function($department){
            CompanyDesignation::factory()->count(2)->create(['department_id' => $department->id])->each(function($designation){
              Admin::where('id', '>', 3)->where('designation_id', null)->inRandomOrder()->limit(2)->update(['designation_id' => $designation->id]);
            });
          });
        });
    }
}
