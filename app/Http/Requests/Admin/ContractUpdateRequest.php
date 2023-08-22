<?php

namespace App\Http\Requests\Admin;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractUpdateRequest extends FormRequest
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
      'subject' => 'required|string|max:100',
      'type_id' => 'required|exists:contract_types,id',
      'assign_to' => 'required|in:Client,Company',
      'client_id' => 'nullable|required_if:assign_to,Client|exists:clients,id',
      'company_id' => 'nullable|required_if:assign_to,Company|exists:companies,id',
      'project_id' => 'nullable|required_if:assign_to,Company|exists:projects,id',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'value' => 'required',
      'status' => 'required|in:'.implode(',', $this->contract->getPossibleStatuses()),
      'description' => 'nullable|string|max:1000',
      'termination_reason' => 'required_if:status,Terminated|max:100',
    ];
  }
}
