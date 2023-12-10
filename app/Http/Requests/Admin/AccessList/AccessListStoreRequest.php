<?php

namespace App\Http\Requests\Admin\AccessList;

use Illuminate\Foundation\Http\FormRequest;

class AccessListStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  // prepare
  protected function prepareForValidation()
  {
    $this->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'accessible_programs' => 'required',
    ], $this->messages());

    $this->merge([
      'is_permanent_access' => $this->boolean('is_permanent_access'),
      'granted_till' => $this->boolean('is_permanent_access') ? null : $this->granted_till,
      'accessible_programs' => explode(',', $this->accessible_programs),
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'is_permanent_access' => 'required|boolean',
      'granted_till' => 'nullable|required_if:is_permanent_access,false|date',
      'accessible_programs' => 'required|array',
      'accessible_programs.*' => 'required|exists:programs,id',
    ];
  }

  // custom messages
  public function messages(): array
  {
    return [
      'users.required' => 'Please select at least one user',
      'users.*.required' => 'Please select at least one user',
      'users.*.exists' => 'Please select at least one user',
      'is_permanent_access.required' => 'Please select a value for is_permanent_access',
      'is_permanent_access.boolean' => 'Please select a valid value for is_permanent_access',
      'granted_till.required_if' => 'Please select a date',
      'granted_till.date' => 'Please select a valid date',
      'accessible_programs.required' => 'Please select at least one program',
      'accessible_programs.*.required' => 'Please select at least one program',
      'accessible_programs.*.exists' => 'Please select at least one program',
    ];
  }
}
