<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class ContractNotificationRequest extends FormRequest
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
      'enable_notifications' => 'nullable',
      'emails' => 'required_if:enable_notifications,1|array',
      'emails.*' => 'nullable|required_if:enable_notifications,1|exists:admins,id',
      'cycle_unit_value' => 'nullable|required_if:enable_notifications,1|numeric|min:1',
      'cycle_unit_name' => 'nullable|required_if:enable_notifications,1|in:Days,Weeks,Months',
      'cycle_count' => 'nullable|required_if:enable_notifications,1|min:1',
    ];
  }
}
