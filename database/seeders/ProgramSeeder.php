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
          $p->users()->attach(Admin::inRandomOrder()->has('programs', '0')->limit(2)->get(), ['added_by' => 1]);
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
            'description' => 'Four touring pavilions to encourage dialogue with the community about culture, art.  Each pavilion will include digital art and cultural programming and is a blend of gallery, open-air classroom, workshop, lecture and socializing space that opens up a world of creativity for all. ',
          ],
          [
            'name' => 'Joyous Gardens',
            'parent_id' => null,
            'program_code' => 'JOY',
            'description' => 'Joyous Gardens provide spaces for creative play as a core part of the healthy and vibrant urban life in Riyadh.  Featuring artistic-designed playgrounds and urban elements located in the parks all over the city, the green areas of the city are transformed into vibrant, inspiring and educational places that spark creativity and adventure and encourage exploration of the natural world. ',
          ],
          [
            'name' => 'Jewels in Riyadh',
            'parent_id' => null,
            'program_code' => 'JEW',
            'description' => 'Jewels in Riyadh transforms the city into an open-air gallery for Contemporary Public Art, offering twenty-four permanent artworks by great art masters to prove contemporary sculpture’s ability in transforming urban spaces and the city.  The choice to place these monumental artworks in civic as well as historical sites generates a natural fusion between the history of Riyadh, its present and its future. ',
          ],
          [
            'name' => 'Welcoming Gateways',
            'parent_id' => null,
            'program_code' => 'WEL',
            'description' => 'A suite of massive artworks interventions sided along key access routes to the city heralding the entrance to Riyadh as a City of Culture. Riyadh’s Welcoming Gateways set the tone for a city where art, architecture and creativity are essential components of a vibrant and global city.  Welcoming Gateways will develop contemporary gateways to the City of Riyadh, as landmarks for incoming and outgoing travelers and citizens. International Architecture firms and local artists collaboration. ',
            'children' => [
            [
              'name' => 'West Entrance Gateway',
              'parent_id' => null,
              'program_code' => 'WST',
              'description' => '',
            ],
            [
              'name' => 'Dammam Road Gateway',
              'parent_id' => null,
              'program_code' => 'DMM',
              'description' => '',
            ],
            [
              'name' => 'Hayer Road Gateway',
              'parent_id' => null,
              'program_code' => 'HYR',
              'description' => '',
            ],
            [
              'name' => 'Qassim Road Gateway',
              'parent_id' => null,
              'program_code' => 'QSM',
              'description' => '',
            ],
            [
              'name' => 'King Salman Road Gateway',
              'parent_id' => null,
              'program_code' => 'KSM',
              'description' => '',
            ]
          ]
          ],
          [
            'name' => 'Art on the Move',
            'parent_id' => null,
            'program_code' => 'AOM',
            'description' => 'A series of distinctive large-scale and high-impact art interventions designed to support the wayfinding experience of Riyadh.  Artworks will be integrated into the core infrastructure of the city, such as intersections and roundabouts and placed at major roads and rail intersections to provide a truly joyful every day experience.  19 locations, 19 artworks ',
          ],
          [
            'name' => 'Art in Transit',
            'parent_id' => null,
            'program_code' => 'AIT',
            'description' => 'The Art in Transit program integrates public art into the core fabric of Riyadh’s Metro, BRT and Bus systems with stations, stops, platforms and infrastructure offering opportunities for artistic interventions. The public art will make the public and commuters feel welcome, create highly distinctive locations and ensure that every journey is an experience.',
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
            'children' => [
                [
                    'name' => 'Hidden River Art Trail',
                    'parent_id' => null,  // This will be dynamically set by the seeder
                    'program_code' => 'HAT',
                    'description' => 'The Hidden River Art Trail will create a red thread that runs through the Wadis and the City, engaging residents and visitors through the permanent installation of the sculptures conceived by performing artists during Tuwaiq Sculpture, activating synergy between Riyadh Art Programs.',
                ],
                [
                    'name' => 'Hidden River Illuminated Bridges',
                    'parent_id' => null,  // This will be dynamically set by the seeder
                    'program_code' => 'HIB',
                    'description' => 'Light Artists and Designers illuminate key bridges across the city and highlight not only the structural configuration of their creative canvas, but the bridge itself as a physical, social and emotional connector within the landscape. A distinctive and vibrant experience for everybody.',
                ],
            ]
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
            'children' => [
                [
                    'name' => 'Noor Riyadh Festival',
                    'parent_id' => null,  // This will be dynamically set by the seeder
                    'program_code' => 'NRF',
                    'description' => '',  // You can add a description here if needed
                ],
            ]
          ],
          [
            'name' => 'River of Light',
            'parent_id' => null,
            'program_code' => 'RIV',
            'description' => '',
          ],
          [
            'name' => 'Tuwaiq Sculpture',
            'parent_id' => null,
            'program_code' => 'TS',
            'description' => 'Tuwaiq Sculpture ...', // existing description
            'children' => [
              [
                'name' => 'Tuwaiq Sculpture Symposium', // example subchild name
                'parent_id' => null, // this will be updated dynamically to the ID of 'Tuwaiq Sculpture Symposium'
                'program_code' => 'TSS', // example code for subchild
                'description' => 'Description for subchild of Tuwaiq Sculpture Symposium.', // example description for subchild
                // You can even nest more children here if required
              ],
            ],
          ],
        ],
      ],
      // Add more predefined values as needed
    ];

    foreach ($predefinedValues as $parentValues) {
      $parentProgram = Program::factory()
          ->predefinedValues(Arr::except($parentValues, 'children'))
          ->create();

      if (isset($parentValues['children']) && is_array($parentValues['children'])) {
          foreach ($parentValues['children'] as $childValues) {
              $childValues['parent_id'] = $parentProgram->id;

              $childProgram = Program::factory()
                  ->predefinedValues(Arr::except($childValues, 'children'))
                  ->create();

              if (isset($childValues['children']) && is_array($childValues['children'])) {
                  foreach ($childValues['children'] as $subChildValues) {
                      $subChildValues['parent_id'] = $childProgram->id;

                      $subChildProgram = Program::factory()
                          ->predefinedValues(Arr::except($subChildValues, 'children'))
                          ->create();
                  }
              }
          }
      }
  }




    // foreach ($predefinedValues as $parentValues) {
    //   $parentProgram = Program::factory()
    //     ->predefinedValues(Arr::except($parentValues, 'children'))
    //     ->create();

    //   $parentProgram->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);
    //   //$parentProgram->users()->attach(Admin::where('id', 1)->get(), ['added_by' => 1]);

    //   if (isset($parentValues['children'])) {
    //     foreach ($parentValues['children'] as $childValues) {
    //       $childValues['parent_id'] = $parentProgram->id;
    //       $childProgram = Program::factory()
    //         ->predefinedValues($childValues)
    //         ->create();

    //       $childProgram->users()->attach(Admin::where('id', '!=', 1)->inRandomOrder()->limit(3)->get(), ['added_by' => 1]);
    //       //$childProgram->users()->attach(Admin::where('id', 1)->get(), ['added_by' => 1]);

    //     }
    //   }
    // }
  }
}
