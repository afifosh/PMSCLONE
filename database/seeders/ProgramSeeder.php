<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Program;
use App\Models\RFPDraft;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Program::factory()->count(12)->create()->each(function ($program) {
        $program->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);

        Program::factory()->count(rand(0, 2))->create(['parent_id' => $program->id])->each(function($p){
          $p->users()->attach(Admin::inRandomOrder()->has('programs', '0')->limit(2)->get(), ['added_by' => 1]);
        });

        RFPDraft::factory()->count(1)->create(['program_id' => $program->id]);
      });
    }
}
