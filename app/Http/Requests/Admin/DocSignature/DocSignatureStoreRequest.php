<?php

namespace App\Http\Requests\Admin\DocSignature;

use Illuminate\Foundation\Http\FormRequest;

class DocSignatureStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation()
  {
    $this->merge([
      'is_signature' => $this->boolean('signature'),
      'signer_type' => 'App\Models\\' . ($this->signer_type ? $this->signer_type : 'Admin'),
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
      'doc' => ['nullable','integer'],
      'is_signature' => 'required|boolean',
      'signer_position' => 'required|string',
      'signer_id' => 'required|integer',
      'signer_type' => 'required|string',
      'signed_at' => 'required|date',
    ];
  }
}
