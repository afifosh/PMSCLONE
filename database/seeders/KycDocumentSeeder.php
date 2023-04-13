<?php

namespace Database\Seeders;

use App\Models\KycDocument;
use Illuminate\Database\Seeder;

class KycDocumentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    KycDocument::create([
      'title' => 'Company CR',
      'required_from' => 3,
      'status' => 1,
      'fields' => json_decode('[
              {
                  "type": "text",
                  "label": "CR Number",
                  "is_required": "1"
              },
              {
                  "type": "date",
                  "label": "CR Issue Date",
                  "is_required": "1"
              },
              {
                  "type": "date",
                  "label": "CR Expiry Date",
                  "is_required": "1"
              },
              {
                  "type": "file",
                  "label": "CR Certificate",
                  "is_required": "1"
              }
          ]', true),
    ]);

    KycDocument::create([
      'title' => 'VAT Certificate',
      'required_from' => 3,
      'status' => 1,
      'fields' => json_decode('[
            {
                "type": "number",
                "label": "VAT Number",
                "is_required": "1"
            },
            {
                "type": "file",
                "label": "VAT Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);
  }
}
