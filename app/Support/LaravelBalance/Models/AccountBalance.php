<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Config;

class AccountBalance extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'account_balances';

    protected $fillable = [
      'account_number',
      'currency',
      'balance',
    ];

    public function setAccountNumberAttribute($value)
    {
      $value = $value ? $value : $this->createUniqueAccountNumber();
      return $this->attributes['account_number'] = $value;
    }

    // create 16 digits unique account number
    protected function createUniqueAccountNumber()
    {
      $accountNumber = rand(1000000000000000, 9999999999999999);
      $account = AccountBalance::where('account_number', $accountNumber)->exists();
      if($account){
        $this->createUniqueAccountNumber();
      }
      return $accountNumber;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }


    /**
     * get account balance holders (AccountBalanceHolderInterface)
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function holders(): MorphToMany
    {
      return $this->morphToMany(AccountBalanceHolder::class, 'holder', 'account_balance_holders', 'account_balance_id', 'holder_id');
    }

    /**
     * return collection of AccountBalanceHolderInterface
     */
    // public function getHolders(): Collection
    // {
    //     return $this->holders()->get();
    // }

    /**
     * @return Money
     */
    // public function getBalance(): Money
    // {
    //     return new Money($this->balance, $this->getCurrency());
    // }

    /**
     * @return Currency
     */
    // public function getCurrency(): Currency
    // {
    //     return new Currency($this->currency);
    // }

    /**
     * @param Transaction $transaction
     */
    // public function addTransaction(Transaction $transaction)
    // {
    //     $transaction->setAccountBalance($this);
    // }

    // public function updateBalance(Money $balance)
    // {
    //     $this->balance = $balance->getAmount();
    // }
}
