<?php

namespace App\Support\LaravelBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Akaunting\Money\Currency;
use App\Support\Money;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
      'balance'
    ];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    CONST PERMISSIONS = [
      '1' => 'Pay Regular Invoice',
      '2' => 'Pay Reverse Charge',
      '3' => 'Pay Withholding Tax',
    ];

    public function getBalanceAttribute($value)
    {
      return $value / 100;
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

    public function scopeApplyRequestFilters($q)
    {
      $q->when(request()->dependent == 'invoice_id' && request()->dependent_2_col == 'inv-type' && request()->dependent_id && request()->dependent_2, function ($q) {
        $q->whereHas('programs', function ($q) {
          $q->whereHas('contracts', function ($q) {
            $q->when(request()->dependent_2 == 'Invoice', function ($q) {
              $q->whereHas('invoices', function ($q) {
                $q->where('invoices.id', request()->dependent_id);
              });
            })->when(request()->dependent_2 == 'AuthorityInvoice', function ($q) {
              $q->whereHas('invoices', function ($q) {
                $q->whereHas('authorityInvoice', function ($q) {
                  $q->where('authority_invoices.id', request()->dependent_id);
                });
              });
            });
          });
        });
      })->when(request()->dependent_3_col == 'pay-type' && request()->dependent_3 && request()->dependent_2_col == 'inv-type', function ($q) {
        $q->when(request()->dependent_2 == 'Invoice' && (request()->dependent_3 == 'Full' || request()->dependent_3 == 'Partial'), function ($q) {
          // user is trying to pay tab 1 invoice, so return the accounts that have permission to pay tab 1 invoices
          $q->whereHas('permissions', function ($q) {
            $q->where('permission', 1);
          });
        })
        ->when(request()->dependent_2 == 'AuthorityInvoice' && request()->dependent_2_col == 'inv-type' && request()->dependent_3_col = 'pay-type' , function ($q) {
          // user is trying to pay tab 3 invoices (wht/rc),
          $q->when(request()->dependent_3 == 'Full', function ($q) {
            // user is paying both wht and rc, so return the accounts that have permission to pay both wht and rc
            $q->whereHas('permissions', function ($q) {
              $q->where('permission', 2);
            })->whereHas('permissions', function ($q) {
              $q->where('permission', 3);
            });
          })
          ->when(request()->dependent_3 == 'wht', function ($q) {
            // user is paying only wht, so return the accounts that have permission to pay only wht
            $q->whereHas('permissions', function ($q) {
              $q->where('permission', 3);
            });
          })
          ->when(request()->dependent_3 == 'rc', function ($q) {
            // user is paying only rc, so return the accounts that have permission to pay only rc
            $q->whereHas('permissions', function ($q) {
              $q->where('permission', 2);
            });
          });
        });
      });
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
      return $this->hasMany(AccountBalancePermission::class);
    }
}
