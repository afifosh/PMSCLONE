<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\Tax;
use Illuminate\Foundation\Http\FormRequest;

class PhaseStoreRequest extends FormRequest
{
  /**
   * The tax rates for the phase. will be used in validation and controller as well
   */
  public $taxes = null;

  /**
   * calculated tax amount for the phase
   */
  public $calculated_tax_amount = 0;

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
      'is_manual_tax' => $this->boolean('is_manual_tax'),
      'manual_tax_amount' => $this->boolean('is_manual_tax') ? $this->manual_tax_amount : 0,
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $this->taxes = Tax::whereIn('id', filterInputIds($this->phase_taxes ?? []))->where('is_retention', false)->where('status', 'Active')->get();
    $fixed_tax = $this->taxes->where('type', 'Fixed')->sum('amount');
    $percent_tax = $this->taxes->where('type', 'Percent')->sum('amount');
    $this->calculated_tax_amount = $fixed_tax + ($percent_tax * $this->estimated_cost / 100);

    $rules = [
      'stage_id' => 'required|exists:contract_stages,id,contract_id,' . $this->contract_id,
      'name' => 'required|string|max:255|unique:contract_phases,name,NULL,id,stage_id,' . $this->stage_id,
      'estimated_cost' => [
        'required',
        'numeric',
        'gt:0',
        //'max:' .  ($this->contract->remaining_amount - $this->calculated_tax_amount))
      ],
      'total_cost' => [
        'required',
        'numeric',
        'gt:0',
        //'max:' . ($this->contract->remaining_amount))
      ],
      'phase_taxes' => 'nullable|array',
      'phase_taxes.*' => 'nullable|exists:taxes,id,is_retention,false',
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->contract->end_date,
      'is_manual_tax' => 'required|boolean',
      'manual_tax_amount' => 'nullable|required_if:is_manual_tax,true|numeric',
    ];

    if ($this->is_manual_tax && abs($this->manual_tax_amount - $this->calculated_tax_amount) > 1) {
      // manual tax and tax_amount should not have difference more than 1
        $rules['manual_tax_amount'] .= '|between:' . ($this->calculated_tax_amount - 1) . ',' . ($this->calculated_tax_amount + 1);
    }

    return $rules;
  }

  public function messages()
  {
    return [
      'estimated_cost.max' => '',
    ];
  }
}
