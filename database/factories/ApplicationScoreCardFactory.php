<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationScoreCard>
 */
class ApplicationScoreCardFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->word,
      'meta' => [
        'sections' => [
          [
            'id' => 'qgrq134A', //random unique id
            'parent_id' => null, // null for root section
            'name' => 'Experience',
            'weight' => 50, // 50%
            'questions' => [
              [
                'question' => 'Relevance of completed work',
                'weight' => 50, // 50%
                'max_score' => 3,// will calculated on run time.
                'scoring_options' => [
                  ['scoring_guide' => 'Option 1', 'score' => 1],
                  ['scoring_guide' => 'Option 2', 'score' => 2],
                  ['scoring_guide' => 'Option 3', 'score' => 3]
                ]
              ],
              [
                'question' => 'Details of the similar projects',
                'weight' => 50, // 50%
                'scoring_options' => [
                  ['scoring_guide' => 'In country', 'score' => 10],
                  ['scoring_guide' => 'In GCC', 'score' => 20],
                  ['scoring_guide' => 'International', 'score' => 30]
                ]
              ],
              [
                'question' => 'Question 3',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ]
            ]
          ],
          [
            'name' => 'Section 2',
            'questions' => [
              [
                'question' => 'Question 1',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ],
              [
                'question' => 'Question 2',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ],
              [
                'question' => 'Question 3',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ]
            ]
          ],
          [
            'name' => 'Section 3',
            'questions' => [
              [
                'question' => 'Question 1',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ],
              [
                'question' => 'Question 2',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ],
              [
                'question' => 'Question 3',
                'options' => [
                  ['label' => 'Option 1', 'score' => 1],
                  ['label' => 'Option 2', 'score' => 2],
                  ['label' => 'Option 3', 'score' => 3]
                ]
              ]
            ]
          ]
        ]
      ]
    ];
  }
}
