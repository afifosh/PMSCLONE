<?php

namespace App\Http\Requests\Admin\Contract\Stage;

use Illuminate\Foundation\Http\FormRequest;

class StageStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Prepare the data for validation.
   */
  public function prepareForValidation()
  {
    $this->merge([
      'is_budget_planned' => $this->is_budget_planned ?? 0,
      'stage_amount' => $this->is_budget_planned ? $this->stage_amount : null,
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
      'name' => 'required|string|max:255|unique:contract_stages,name,NULL,id,contract_id,' . $this->contract->id,
      'is_budget_planned' => 'nullable|boolean',
      'stage_amount' => ['nullable', 'required_if:is_budget_planned,1', 'numeric', 'gt:0', 'max:' . $this->contract->remaining_amount],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date' . ($this->contract->end_date ? ('|before_or_equal:' . $this->contract->end_date) : ''),
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
      'start_date.after_or_equal' => 'The start date must be a date after or equal to contract start date.',
      'due_date.after' => 'The due date must be a date after start date.',
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ];
  }
}
