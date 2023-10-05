<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\Tax;
use Illuminate\Foundation\Http\FormRequest;

class PhaseUpdateRequest extends FormRequest
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
    $taxes = Tax::whereIn('id', filterInputIds($this->phase_taxes ?? []))->where('is_retention', false)->where('status', 'Active')->get();
    $fixed_tax = $taxes->where('type', 'Fixed')->sum('amount');
    $percent_tax = $taxes->where('type', 'Percent')->sum('amount');
    $tax_amount = $fixed_tax + ($percent_tax * $this->estimated_cost / 100);

    return [
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $this->phase->id . ',id,stage_id,' . $this->phase->stage_id,
      'estimated_cost' => ['required', 'numeric', 'gt:0', 'max:' . $this->stage->remaining_amount - $tax_amount + $this->phase->estimated_cost],
      'total_cost' => ['required', 'numeric', 'gt:0', 'max:' . $this->phase->stage->remaining_amount + $this->phase->total_cost],
      'phase_taxes' => 'nullable|array',
      'phase_taxes.*' => 'nullable|exists:taxes,id,is_retention,false',
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->phase->stage->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->phase->stage->due_date,
    ];
  }

  public function messages()
  {
    return [
      'estimated_cost.max' => '',
      'due_date.before_or_equal' => 'The due date must be a date before or equal to state due date.'
    ];
  }
}
