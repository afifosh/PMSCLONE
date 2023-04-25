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
      'description' => 'Company CR Info',
      'is_expirable' => 1,
      'is_mendatory' => 1,
      'expiry_date_title' => 'CR Expiry Date',
      'is_expiry_date_required' => 1,
      'fields' => json_decode('[
            {
                "id": "643a377453d16",
                "type": "text",
                "label": "CR Number",
                "is_required": "1"
            },
            {
                "id": "643a377453d19",
                "type": "date",
                "label": "CR Issue Date",
                "is_required": "1"
            },
            {
                "id": "643a377453d1a",
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
      'description' => 'VAT Certificate Info',
      'is_expirable' => 0,
      'is_mendatory' => 1,
      'fields' => json_decode('[
            {
                "id": "643a379b2c56a",
                "type": "number",
                "label": "VAT Number",
                "is_required": "1"
            },
            {
                "id": "643a379b2c56c",
                "type": "file",
                "label": "VAT Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);
  }
}
