<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskFollower;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Project::factory(9)->create()->each(function ($project) {
      $project->members()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get());
      Task::factory(5)->create(['project_id' => $project->id])->each(function ($task) {
        TaskFollower::factory(3)->create(['task_id' => $task->id]);
        TaskAssignee::factory(3)->create(['task_id' => $task->id]);
      });
      // $project->tasks()->createMany(\App\Models\Task::factory(5)->make()->toArray())->each(function ($task) {
      //   TaskFollower::factory(3)->create(['task_id' => $task->id]);
      //   TaskAssignee::factory(3)->create(['task_id' => $task->id]);
      // });
    });
  }
}
