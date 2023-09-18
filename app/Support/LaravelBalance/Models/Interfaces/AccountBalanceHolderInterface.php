<?php

namespace App\Support\LaravelBalance\Models\Interfaces;

use App\Support\LaravelBalance\Models\AccountBalance;

interface AccountBalanceHolderInterface
{
    public function getAccount(string $currency): ?AccountBalance;

    public function addAccountBalance(AccountBalance $accountBalance);
}
