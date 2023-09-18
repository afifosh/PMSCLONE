<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Akaunting\Money\Money;

class Transaction extends Model
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table;

  /**
   * Creates a new instance of the model.
   *
   * @param array $attributes
   */
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (!$this->created_at) {
      $this->created_at = (new \DateTime())->format('Y-m-d H:i:s');
    }
    $this->table = Config::get('vuer-account-balance.account_balance_transactions_table');
  }

  protected $fillable = [
    'account_balance_id',
    'amount',
    'type',
    'title',
    'data',
    'remaining_balance',
    'description',
    'related_type',
    'related_id',
  ];

  protected $casts = [
    'data' => 'array',
    'created_at' => 'datetime: h:i A d M, Y ',
    'updated_at' => 'datetime: h:i A d M, Y',
  ];

  public function printableAmount()
  {
    return Money::{$this->accountBalance->currency}($this->amount, false)->format();
  }

  public function printableBalance()
  {
    return Money::{$this->accountBalance->currency}($this->remaining_balance, false)->format();
  }

  public function getAmount(): Money
  {
    return new Money((int)$this->amount, $this->getAccountBalance()->getCurrency());
  }

  /**
   * @return AccountBalance
   */
  public function getAccountBalance(): AccountBalance
  {
    return $this->accountBalance;
  }

  /**
   * @param AccountBalance $accountBalance
   */
  public function setAccountBalance(AccountBalance $accountBalance)
  {
    $this->accountBalance()->associate($accountBalance);
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function accountBalance()
  {
    return $this->belongsTo(AccountBalance::class);
  }

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  public function related()
  {
    return $this->morphTo();
  }
}
