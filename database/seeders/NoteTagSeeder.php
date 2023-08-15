<?php

namespace Database\Seeders;

use App\Models\NoteTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NoteTag::insert([
          ['name' => 'Low', 'color' => 'blue'],
          ['name' => 'Medium', 'color' => 'yellow'],
          ['name' => 'High', 'color' => 'red']
        ]);
    }
}
