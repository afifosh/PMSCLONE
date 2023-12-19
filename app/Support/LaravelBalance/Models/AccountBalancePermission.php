<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBalancePermission extends Model
{
  protected $fillable = [
    'account_balance_id',
    'permission',
  ];

  protected $casts = [
    'created_at' => 'datetime: h:i A d M, Y ',
    'updated_at' => 'datetime: h:i A d M, Y',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function accountBalance()
  {
    return $this->belongsTo(AccountBalance::class);
  }
}
