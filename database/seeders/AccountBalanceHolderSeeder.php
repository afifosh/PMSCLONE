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


        // Select all Program instances
        $allPrograms = Program::all();

        // Create a unique Account Number for the 'Reverse Charge' Account
        $uniqueAccountNumber = AccountBalance::createUniqueAccountNumber();

        // Create a single 'Reverse Charge' AccountBalance
        $reverseChargeAccountBalance = AccountBalance::factory()->create([
            'name' => "Reverse Charge",  // Set the name to "Reverse Charge"
            'account_number' => $uniqueAccountNumber,
        ]);

        // Create the 'Reverse Charge' permission for this account balance
        $reverseChargeAccountBalance->permissions()->create([
            'permission' => 2 // Pay Reverse Charge
        ]);

        // Create an AccountBalanceHolder linking each All Program to the Reverse Charge AccountBalance
        $allPrograms->each(function ($Program) use ($reverseChargeAccountBalance) {
            AccountBalanceHolder::create([
                'account_balance_id' => $reverseChargeAccountBalance->id,
                'holder_type' => get_class($Program),
                'holder_id' => $Program->id,
            ]);
        });


        // Select all Program instances where parent_id = 1
        $programs = Program::where('parent_id', 1)->get();

        $programs->each(function ($program) {

            $uniqueAccountNumber = AccountBalance::createUniqueAccountNumber();

            // Create an AccountBalance for each Program
            $accountBalance = AccountBalance::factory()->create([
                'name' => $program->name,  // Set the name based on the Program's name
                'account_number' => $uniqueAccountNumber
            ]);

            // create permissions for the account balance

            $accountBalance->permissions()->createMany([
                ['permission' => 1], // Pay Regular Invoice
            //  ['permission' => 2], // Pay Reverse Charge
                ['permission' => 3]  // Pay Withholding Tax
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
