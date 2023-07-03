<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(9)->create()->each(function($project){
            $project->members()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get());
        });
    }
}
