<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBalanceHolder extends Model
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'account_balance_holders';

  protected $fillable = [
    'holder_id',
    'holder_type',
    'account_balance_id',
  ];

  public function holders()
  {
    return $this->morphTo();
  }
}
