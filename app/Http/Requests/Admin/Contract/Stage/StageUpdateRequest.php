<?php

namespace App\Http\Requests\Admin\Contract\Stage;

use Illuminate\Foundation\Http\FormRequest;

class StageUpdateRequest extends FormRequest
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
      'name' => 'required|string|max:255|unique:contract_stages,name,' . $this->stage->id . ',id,contract_id,' . $this->contract->id,
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'name.unique' => 'The stage name has already been taken.',
    ];
  }
}
