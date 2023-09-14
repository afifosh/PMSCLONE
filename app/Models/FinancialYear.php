<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Money;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Vuer\LaravelBalance\Models\AccountBalance;
use Vuer\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;

class FinancialYear extends Model implements AccountBalanceHolderInterface
{
  use HasFactory;

  protected $fillable = [
    'start_date',
    'end_date',
    'label',
    'initial_balance',
  ];

  protected $casts = [
    'start_date' => 'datetime:d M, Y',
    'end_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function getInitialBalanceAttribute($value)
  {
    if($value === null){
      return null;
    }

    return Money::{$this->defaultCurrencyAccount->currency}($value, false)->format();
  }

  public function setInitialBalanceAttribute($value)
  {
    return $this->attributes['initial_balance'] = $value * 100;
  }

  public function budget()
  {
    return $this->hasMany(Budget::class);
  }

  public function accountBalances()
  {
    return $this->morphMany(AccountBalance::class, 'holder');
  }

  public function defaultCurrencyAccount(): HasOne
  {
    return $this->hasOne(AccountBalance::class, 'holder_id')->where('holder_type', self::class);
  }

  public function getAccount(string $currency): ?AccountBalance
  {
    return $this->accountBalances()->where('currency', $currency)->first();
  }

  public function addAccountBalance(AccountBalance $accountBalance)
  {
    $accountBalance->holder()->associate($this);
    $accountBalance->save();
  }
}
