<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Supported services
         */
        $services = [
            [
                'service_label' => 'Amazon SES',
                'service' => 'ses',
                'is_active' => true,
            ],
            [
                'service_label' => 'Mailgun',
                'service' => 'mailgun',
            ],
            [
                'service_label' => 'SMTP',
                'service' => 'smtp',
            ],
            [
                'service_label' => 'Sendmail',
                'service' => 'sendmail',
            ],
            [
                'service_label' => 'Mailtrap',
                'service' => 'mailtrap',
            ],
        ];

        foreach ($services as $service) {
            DB::table('email_services')->insert([
                'service_label' => $service['service_label'],
                'service' => $service['service'],
                'is_active' => $service['is_active'] ?? false,
            ]);
        }
    }
}
