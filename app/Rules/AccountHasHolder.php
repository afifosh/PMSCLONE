<?php

namespace App\Rules;

use App\Support\LaravelBalance\Models\AccountBalance;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class AccountHasHolder implements Rule
{
  private $holder_id;
  private $holder_type;
  private $permissions;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($holder_id, $holder_type, $permissions = [1, 2, 3])
  {
    $this->holder_id = $holder_id;
    $this->holder_type = $holder_type;
    $this->permissions = $permissions;
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
    $query = AccountBalance::whereHas($this->holder_type, function ($q) {
      $q->where($this->holder_type . '.id', $this->holder_id);
    })
    ->where('id', $value);

    // account must have all the permissions
    foreach ($this->permissions as $permission) {
      $query->whereHas('permissions', function ($q) use ($permission) {
        $q->where('permission', $permission);
      });
    }

    if ($query->exists()) {
      return true;
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
