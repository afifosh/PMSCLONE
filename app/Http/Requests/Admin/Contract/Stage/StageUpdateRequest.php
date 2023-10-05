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
    $startDateBeforeOrEqual = (optional($this->stage->phases->sortBy('start_date')->first())->start_date ?: $this->contract->end_date);
    return [
      'name' => 'required|string|max:255|unique:contract_stages,name,' . $this->stage->id . ',id,contract_id,' . $this->contract->id,
      'stage_amount' => [
        'required',
        'numeric',
        'gte:' . $this->stage->stage_amount - $this->stage->remaining_amount, // gte already distributed to phases.
        'lte:' . $this->contract->remaining_cost($this->stage->stage_amount)
      ],
      'description' => 'nullable|string|max:2000',
      'start_date' => [
        'required',
        'date',
        'after_or_equal:' . $this->contract->start_date,
        ($startDateBeforeOrEqual ? ('before_or_equal:' . $startDateBeforeOrEqual) : ''),
      ],
      'due_date' => [
        'nullable',
        'date',
        'after:start_date',
        ($this->contract->end_date ? ('before_or_equal:' . $this->contract->end_date) : ''),
        'after_or_equal:' . (optional($this->stage->phases->sortByDesc('due_date')->first())->due_date ?: $this->contract->start_date),
      ]
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
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ];
  }
}
