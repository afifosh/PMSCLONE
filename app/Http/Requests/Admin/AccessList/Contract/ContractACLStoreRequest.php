<?php

namespace App\Http\Requests\Admin\AccessList\Contract;

use Illuminate\Foundation\Http\FormRequest;

class ContractACLStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'is_permanent_access' => $this->boolean('is_permanent_access'),
      'granted_till' => $this->boolean('is_permanent_access') ? null : $this->granted_till,
      'is_revoked' => $this->boolean('is_revoked'),
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
      'admin_id' => 'required|exists:admins,id',
      'accessible_id' => 'required|exists:contracts,id',
      'is_permanent_access' => 'required|boolean',
      'granted_till' => 'nullable|required_if:is_permanent_access,false|date',
      'is_revoked' => 'required|boolean',
    ];
  }
}
