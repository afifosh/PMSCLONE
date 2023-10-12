<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Currency;
use App\Support\Money;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountBalance extends Model
{

  use HasFactory, SoftDeletes;
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

    public function getBalanceAttribute($value)
    {
      return $value / 1000;
    }
    public function setBalanceAttribute($value)
    {
      return $this->attributes['balance'] = moneyToInt($value);
    }

    public function setAccountNumberAttribute($value)
    {
      $value = $value ? $value : $this->createUniqueAccountNumber();
      return $this->attributes['account_number'] = $value;
    }

    // create 16 digits unique account number
    public static function createUniqueAccountNumber()
    {
        do {
            $accountNumber = rand(1000000000000000, 9999999999999999);
            $exists = self::where('account_number', $accountNumber)->exists();
        } while ($exists);

        return $accountNumber;
    }

    public function scopeApplyRequestFilters($query)
    {
      //
    }

    public function printableBalance()
    {
      return Money::{$this->currency}($this->balance, true)->format();
    }

    public function printableAccountNumber()
    {
      return preg_replace('/^(\d{4})(\d{4})(\d{4})(\d{4})$/', '$1 $2 $3 $4', $this->account_number);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function related(): HasMany
    {
      return $this->hasMany(AccountBalanceHolder::class);
    }

    public function programs()
    {
      return $this->morphedByMany(Program::class, 'holder', 'account_balance_holders', 'account_balance_id', 'holder_id');
    }

    /**
     * @return Money
     */
    public function getBalance(): Money
    {
        return cMoney($this->balance ? $this->balance : 0, $this->getCurrencySymbol());
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return new Currency($this->currency);
    }

    public function getCurrencySymbol()
    {
      return $this->currency;
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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\AccountBalanceFactory::new();
    }

}
