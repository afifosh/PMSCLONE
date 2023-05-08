<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Program;
use App\Models\RFPDraft;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function runold()
    {
      Program::factory()->count(12)->create()->each(function ($program) {
        $program->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);

        Program::factory()->count(rand(0, 2))->create(['parent_id' => $program->id])->each(function($p){
          $p->users()->attach(Admin::inRandomOrder()->limit(2)->get(), ['added_by' => 1]);
        });

        RFPDraft::factory()->count(1)->create(['program_id' => $program->id]);
      });
    }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $predefinedValues = [
      [
        'name' => 'Riyadh Art',
        'parent_id' => null,
        'program_code' => 'RA',
        'description' => 'Riyadh Art is the first national public art initiative in the Kingdom of Saudi Arabia. Riyadh Art will transform the city of Riyadh into a gallery without walls, and a creative powerhouse for the digital age.',
        'children' => [
          [
            'name' => 'Program Generic Communication',
            'parent_id' => null,
            'program_code' => 'PRG',
            'description' => 'Program generic communication – Used also for outbound communications with other programs / external stakeholders where there’s no subprogram (or multiple subprograms) associated.',
          ],
          [
            'name' => 'Urban Art Lab',
            'parent_id' => null,
            'program_code' => 'UAL',
            'description' => '',
          ],      
          [
            'name' => 'Joyous Gardens',
            'parent_id' => null,
            'program_code' => 'JOY',
            'description' => '',
          ],   
          [
            'name' => 'Jewels in Riyadh',
            'parent_id' => null,
            'program_code' => 'JEW',
            'description' => '',
          ],   
          [
            'name' => 'Welcoming Gateways',
            'parent_id' => null,
            'program_code' => 'WEL',
            'description' => '',
          ],   
          [
            'name' => 'Art on the Move',
            'parent_id' => null,
            'program_code' => 'AOM',
            'description' => '',
          ],   
          [
            'name' => 'Art in Transit',
            'parent_id' => null,
            'program_code' => 'AIT',
            'description' => '',
          ],   
          [
            'name' => 'Urban Flow',
            'parent_id' => null,
            'program_code' => 'UFL',
            'description' => 'Urban Flow integrates public art into the fabric of the citywide network of pedestrian walkways, bridges and footpaths, enticing citizens to experience art and the city on their own terms.',
          ],   
          [
            'name' => 'Hidden River',
            'parent_id' => null,
            'program_code' => 'HDR',
            'description' => '',
          ],   
          [
            'name' => 'Hidden River Art Trail',
            'parent_id' => null,
            'program_code' => 'HAT',
            'description' => 'The Hidden River Art Trail will create a red thread that runs through the Wadis and the City, engaging residents and visitors through the permanent installation of the sculptures conceived by performing artists during Tuwaiq Sculpture, activating synergy between Riyadh Art Programs.',
          ],    
          [
            'name' => 'Hidden River Illuminated Bridges',
            'parent_id' => null,
            'program_code' => 'HIB',
            'description' => 'Light Artists and Designers illuminate key bridges across the city and highlight not only the structural configuration of their creative canvas, but the bridge itself as a physical, social and emotional connector within the landscape. A distinctive and vibrant experience for everybody.',
          ],   
          [
            'name' => 'Garden City',
            'parent_id' => null,
            'program_code' => 'GAR',
            'description' => 'This is a sculpture park for the 21st century, inviting new interpretations of art, technology and environmental engagement for Riyadh’s residents and visitors.',
          ],   
          [
            'name' => 'Riyadh Icon',
            'parent_id' => null,
            'program_code' => 'ICO',
            'description' => 'This ambitious international commission is a visual symbol of the cultural aspirations of Vision 2030 and it heralds a new era of Saudi intention to celebrate a global community that sparks creativity, ignites the art movement, encourages self-expression, and creates an inclusive culture.',
          ],   
          [
            'name' => 'Noor Riyadh',
            'parent_id' => null,
            'program_code' => 'NOO',
            'description' => 'Noor Riyadh is a citywide annual festival of light and art that nurtures creativity, promotes talent and delivers awe-inspiring experiences.',
          ],   
          [
            'name' => 'West Gateway',
            'parent_id' => null,
            'program_code' => 'WST',
            'description' => '',
          ],  
          [
            'name' => 'River of Light',
            'parent_id' => null,
            'program_code' => 'RIV',
            'description' => '',
          ],   
          [
            'name' => 'Tuwaiq Sculpture Symposium',
            'parent_id' => null,
            'program_code' => 'TSS',
            'description' => 'Tuwaiq Sculpture is an annual sculpture symposium that brings local and international artists together to create public artworks in a live setting. Through an interactive program of workshops, school visits, and talks, Tuwaiq Sculpture engages diverse communities and bolsters cultural exchanges. The symposium culminates in an on-site exhibition, with the sculptures enriching the Riyadh Art collection and later becoming a permanent feature of the Saudi capital’s urban fabric.',
          ],                                                                                     
        ],
      ],
      // Add more predefined values as needed
    ];

    foreach ($predefinedValues as $parentValues) {
      $parentProgram = Program::factory()
        ->predefinedValues(Arr::except($parentValues, 'children'))
        ->create();

      $parentProgram->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);
      //$parentProgram->users()->attach(Admin::where('id', 1)->get(), ['added_by' => 1]);

      if (isset($parentValues['children'])) {
        foreach ($parentValues['children'] as $childValues) {
          $childValues['parent_id'] = $parentProgram->id;
          $childProgram = Program::factory()
            ->predefinedValues($childValues)
            ->create();

          $childProgram->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);
          //$childProgram->users()->attach(Admin::where('id', 1)->get(), ['added_by' => 1]);
            
        }
      }
    }
  }
}    
