<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    return $value / 1000;
  }

  public function setInitialBalanceAttribute($value)
  {
    return $this->attributes['initial_balance'] = moneyToInt($value);
  }

  public function budget()
  {
    return $this->hasMany(Budget::class);
  }

  public function accountBalances()
  {
    // this -> account_balance_holders -> account_balance morphicmany
    return $this->morphToMany(AccountBalance::class, 'holder', 'account_balance_holders', 'holder_id', 'account_balance_id');
  }

  public function accountBalance()
  {
    // this -> account_balance_holders -> account_balance take latest one
    return $this->morphToMany(AccountBalance::class, 'holder', 'account_balance_holders', 'holder_id', 'account_balance_id')->latest();//->latestOfMany();
  }

  public function defaultCurrencyAccount()
  {
    return $this->morphToMany(AccountBalance::class, 'holder', 'account_balance_holders', 'holder_id', 'account_balance_id')->latest();
  }

  public function getAccount(string $currency): ?AccountBalance
  {
    return $this->accountBalances()->where('currency', $currency)->first();
  }

  public function addAccountBalance(AccountBalance $accountBalance)
  {
    //
  }
}
