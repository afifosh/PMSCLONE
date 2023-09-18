<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Models\Program;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class AccountBalance extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'account_balances';

    protected $fillable = [
      'name',
      'account_number',
      'currency',
      'balance',
      'creator_id',
      'creator_type',
    ];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
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

    public function printableBalance()
    {
      return Money::{$this->currency}($this->balance, false)->format();
    }

    public function printableAccountNumber()
    {
      return preg_replace('/^(\d{4})(\d{4})(\d{4})(\d{4})$/', '$1 $2 $3 $4', $this->account_number);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }

    public function related(): HasMany
    {
      return $this->hasMany(AccountBalanceHolder::class);
    }

    public function programs(): MorphToMany
    {
      return $this->morphedByMany(Program::class, 'holder', 'account_balance_holders', 'account_balance_id', 'holder_id');
    }

    /**
     * @return Money
     */
    public function getBalance(): Money
    {
        return new Money($this->balance ? $this->balance : 0, $this->getCurrency());
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return new Currency($this->currency);
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction)
    {
        $transaction->setAccountBalance($this);
    }

    public function updateBalance(Money $balance)
    {
        $this->balance = $balance->getAmount();
    }
}
