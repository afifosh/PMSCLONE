<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Innoclapps\Facades\MailableTemplates;

class MailableTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mailables = MailableTemplates::get();

        foreach ($mailables as $mailable) {
            $mailable = new \ReflectionMethod($mailable, 'seed');

            $mailable->invoke(null);
        }
    }
}
