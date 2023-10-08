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

    /*
    /* Contract Required Docs
    */

    KycDocument::create([
      'workflow' => 'Contract Required Docs',
      'title' => 'Certificate of Insurance',
      'required_from' => 0, // from other workflow not related to contract but required for consistency
      'client_type' => 'Both',
      'status' => 1,
      'description' => 'Certificate of Insurance Info',
      'is_expirable' => 1,
      'is_mendatory' => 1,
      'expiry_date_title' => 'Expiry Date',
      'is_expiry_date_required' => 1,
      'fields' => json_decode('[
            {
                "id": "643a377453d30",
                "type": "text",
                "label": "CI Number",
                "is_required": "1"
            },
            {
                "id": "643a377453d31",
                "type": "date",
                "label": "CI Issue Date",
                "is_required": "1"
            },
            {
                "id": "643a377453d32",
                "type": "file",
                "label": "CI Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);

    KycDocument::create([
      'workflow' => 'Contract Required Docs',
      'title' => 'VAT Certificate',
      'required_from' => 3, // from contract workflow
      'client_type' => 'Company',
      'status' => 1,
      'description' => 'VAT Certificate Info',
      'is_expirable' => 0,
      'is_mendatory' => 1,
      'fields' => json_decode('[
            {
                "id": "643a377453d40",
                "type": "number",
                "label": "VAT Number",
                "is_required": "1"
            },
            {
                "id": "643a377453d41",
                "type": "file",
                "label": "VAT Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);


    /*
    /* Invoice Required Docs
    */

    KycDocument::create([
      'workflow' => 'Invoice Required Docs',
      'title' => 'Certificate of Insurance',
      'required_from' => 0, // from other workflow not related to contract but required for consistency
      'client_type' => 'Both',
      'status' => 1,
      'description' => 'Certificate of Insurance Info',
      'is_expirable' => 1,
      'is_mendatory' => 1,
      'expiry_date_title' => 'Expiry Date',
      'is_expiry_date_required' => 1,
      'fields' => json_decode('[
            {
                "id": "in643a3774a50",
                "type": "text",
                "label": "CI Number",
                "is_required": "1"
            },
            {
                "id": "in643a3774a51",
                "type": "date",
                "label": "CI Issue Date",
                "is_required": "1"
            },
            {
                "id": "in643a3774a52",
                "type": "file",
                "label": "CI Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);

    KycDocument::create([
      'workflow' => 'Invoice Required Docs',
      'title' => 'VAT Certificate',
      'required_from' => 3, // from contract workflow
      'client_type' => 'Both',
      'status' => 1,
      'description' => 'VAT Certificate Info',
      'is_expirable' => 0,
      'is_mendatory' => 1,
      'fields' => json_decode('[
            {
                "id": "in643a3774b50",
                "type": "number",
                "label": "VAT Number",
                "is_required": "1"
            },
            {
                "id": "in643a3774b51",
                "type": "file",
                "label": "VAT Certificate",
                "is_required": "1"
            }
        ]', true),
    ]);
  }
}
