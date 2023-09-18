<?php

namespace App\Rules;

use App\Support\LaravelBalance\Models\AccountBalance;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class AccountHasHolder implements Rule
{
  private $holder_id;
  private $holder_type;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($holder_id, $holder_type)
  {
    $this->holder_id = $holder_id;
    $this->holder_type = $holder_type;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    $account = AccountBalance::find($value);
    if ($account) {
      $holder = $account->whereHas($this->holder_type, function ($q) {
        $q->where($this->holder_type.'.id', $this->holder_id);
      })->first();
      if ($holder) {
        return true;
      }
    }
    return false;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return __('Invalid Account');
  }
}
