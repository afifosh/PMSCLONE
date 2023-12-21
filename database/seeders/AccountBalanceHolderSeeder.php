<?php

namespace Database\Seeders;

use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\AccountBalanceHolder;
use App\Models\Program;
use Illuminate\Database\Seeder;

class AccountBalanceHolderSeeder extends Seeder
{
    public function run()
    {
        // Select all Program instances where parent_id = 1
        $programs = Program::where('parent_id', 1)->get();

        $programs->each(function ($program) {

            $uniqueAccountNumber = AccountBalance::createUniqueAccountNumber();

            // Create an AccountBalance for each Program
            $accountBalance = AccountBalance::factory()->create([
                'name' => $program->name,  // Set the name based on the Program's name
                'account_number' => $uniqueAccountNumber,
                'creator_id' => $program->id,
                'creator_type' => get_class($program),
            ]);

            // create permissions for the account balance
            $accountBalance->permissions()->createMany([
              ['permission' => 1],
              ['permission' => 2],
              ['permission' => 3]
            ]);

            // Get all child programs for the current program
            $childPrograms = Program::where('parent_id', $program->id)->get();

            if ($childPrograms->isEmpty()) {
                // If there are no child programs, make the program itself the account holder
                AccountBalanceHolder::create([
                    'account_balance_id' => $accountBalance->id,
                    'holder_type' => get_class($program),
                    'holder_id' => $program->id,
                ]);
            } else {
                // Create an AccountBalanceHolder linking each child Program to the AccountBalance
                $childPrograms->each(function ($childProgram) use ($accountBalance) {
                    AccountBalanceHolder::create([
                        'account_balance_id' => $accountBalance->id,
                        'holder_type' => get_class($childProgram),
                        'holder_id' => $childProgram->id,
                    ]);
                });
            }
        });
    }
}
