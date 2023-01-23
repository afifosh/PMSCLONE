<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $i = 1;
        for($i; $i<= 15 ; $i++){
            User::create(['name' => $faker->name('male').' '.$i, 'email' => 'user'.$i.'@example.com','password' => Hash::make('123456'),'email_verified_at'=>'2022-01-02 17:04:58','created_at' => now(),]);
        }
    }
}
