<?php

namespace App\Support\LaravelBalance\Exceptions;

use Akaunting\Money\Currency;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;

class AccountBalanceLogicException extends \LogicException
{
    public static function accountAlreadyExists(AccountBalanceHolderInterface $accountBalanceHolder, Currency $currency)
    {
        return new self(
            sprintf('The %s account for %s already exists', get_class($accountBalanceHolder), $currency->getCode())
        );
    }
}
