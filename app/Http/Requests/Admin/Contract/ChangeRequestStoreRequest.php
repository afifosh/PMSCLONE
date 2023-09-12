<?php

namespace App\Http\Requests\Admin\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRequestStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'reason' => 'required|string',
      'description' => 'nullable|string',
      'value_action' => 'required|string|in:inc,dec,unchanged',
      'value_change' => 'nullable|required_if:value_action,inc,dec|numeric',
      'currency' => ['required', 'string', Rule::in(array_keys(config('money.currencies')))],
      'timeline_action' => 'required|string|in:inc,dec,unchanged',
      'new_end_date' => 'nullable|required_if:timeline_action,inc,dec|date|after:'.$this->contract->start_date,
    ];
  }
}
