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
        $services = array (
            array(
                'label'     => 'Amazon SES',
                'name'      => 'ses',
                'is_active' => true,
            ),
            array(
                'label' => 'Mailgun',
                'name'  => 'mailgun',
            ),
            array(
                'label' => 'SMTP',
                'name'  => 'smtp',
            ),
            array(
                'label' => 'Sendmail',
                'name'  => 'sendmail',
            ),
            array(
                'label' => 'Mailtrap',
                'name'  => 'mailtrap',
            ),
        );

        foreach ($services as $service) {
            DB::table('email_services')->insert([
                'label'     => $service['label'],
                'name'   => $service['name'],
                'is_active' => $service['is_active'] ?? false,
            ]);
        }
    }
}
