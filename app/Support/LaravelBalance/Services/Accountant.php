<?php

namespace App\Support\LaravelBalance\Services;

use Akaunting\Money\Currency;
use App\Support\LaravelBalance\Exceptions\AccountBalanceLogicException;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\AccountBalanceHolder;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Accountant
{
  public function getAccount(AccountBalanceHolderInterface $accountBalanceHolder, Currency $currency): ?AccountBalance
  {
    return $accountBalanceHolder->getAccount($currency->getCode());
  }

  public function getAccountOrCreate(AccountBalanceHolderInterface $accountBalanceHolder, Currency $currency): AccountBalance
  {
    $account = $this->getAccount($accountBalanceHolder, $currency);

    if (null === $account) {
      $account = $this->createAccount($accountBalanceHolder, $currency);
    }

    return $account;
  }

  /**
   * @param AccountBalanceHolderInterface|Collection $accountBalanceHolder
   * @param array $data ['currency' => 'USD', 'account_number' => '1234567890', 'name' => 'My Account']
   * @return AccountBalance
   * @throws AccountBalanceLogicException
   */

  public function createAccount(AccountBalanceHolderInterface|Collection $accountBalanceHolder, array $data): AccountBalance
  {
    // disabled this check because one account can be shared by multiple holders and one holder can have multiple accounts
    // if (null !== $this->getAccount($accountBalanceHolder, $currency)) {
    //     throw AccountBalanceLogicException::accountAlreadyExists($accountBalanceHolder, $currency);
    // }

    $accountBalance = AccountBalance::create([
      'currency' => $data['currency'] ?? config('money.defaults.currency'),
      'account_number' => $data['account_number'] ?? null,
      'name' => $data['name'] ?? null
    ]);

    if ($accountBalanceHolder instanceof Collection) {
      foreach ($accountBalanceHolder as $holder) {
        $this->createHolder($accountBalance, $holder);
      }
    } else {
      $this->createHolder($accountBalance, $accountBalanceHolder);
    }

    return $accountBalance;
  }

  public function createHolder(AccountBalance $accountBalance, AccountBalanceHolderInterface|Model $accountBalanceHolder): void
  {
    AccountBalanceHolder::create([
      'account_balance_id' => $accountBalance->id,
      'holder_id' => $accountBalanceHolder->id,
      'holder_type' => get_class($accountBalanceHolder),
    ]);
  }

  /**
   * Create 16 digits unique account number with prefix and suffix
   * @param $prefix
   * @param $suffix
   * @return string
   */
  public function createUniqueAccountNumber($prefix = null, $suffix = null)
  {
    // add prefix and suffix to account number if they are not null, final account number should be 16 digits
    $prefixDigits = strlen((string)$prefix);
    $suffixDigits = strlen((string)$suffix);

    // Calculate the number of digits needed to fill the remaining space
    $remainingDigits = 16 - $prefixDigits - $suffixDigits;

    // Ensure the fill digits are positive
    if ($remainingDigits < 0) {
      throw new Exception("Prefix and suffix combined exceed 16 digits.");
    }

    // Generate random digits for the remaining space
    $generatedNumber = mt_rand(0, (10 ** $remainingDigits) - 1);

    // Create the final account number
    $accountNumber = $prefix . str_pad($generatedNumber, $remainingDigits, '0', STR_PAD_LEFT) . $suffix;

    // Check if the account number already exists in the database and generate a new one if it does recursively
    $account = AccountBalance::where('account_number', $accountNumber)->first();
    if ($account) {
      return $this->createUniqueAccountNumber($prefix, $suffix);
    }

    return $accountNumber;
  }
}
