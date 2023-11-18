<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;

class PhaseUpdateRequest extends FormRequest
{
  /**
   * The tax rates for the phase. will be used in validation and controller as well
   */
  public $tax_rates = null;

  /**
   * calculated tax amount for the phase
   */
  public $calculated_tax_amount = 0;

  public $total_tax_amount = 0;

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
    $tax_ids = array_column($this->taxes, 'phase_tax');
    $this->tax_rates = InvoiceConfig::whereIn('id', filterInputIds($tax_ids))->activeTaxes()->get();
    // $fixed_tax = $this->taxes->where('type', 'Fixed')->sum('amount');
    // $percent_tax = $this->taxes->where('type', 'Percent')->sum('amount');
    // $this->calculated_tax_amount = $fixed_tax + ($percent_tax * $this->estimated_cost / 100);

    $rules = [
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $this->phase->id . ',id,stage_id,' . $this->phase->stage_id,
      'estimated_cost' => [
        'required',
        'numeric',
        ($this->phase->stage->is_budget_planned ? 'gt:0' : 'gte:0'),
        //'max:' . ($this->phase->stage->is_budget_planned ? ($this->phase->stage->remaining_amount - $this->calculated_tax_amount + $this->phase->total_cost) : ($this->contract->remaining_amount - $this->calculated_tax_amount + $this->phase->total_cost))
      ],
      'total_cost' => [
        'required',
        'numeric',
        ($this->phase->stage->is_budget_planned ? 'gt:0' : 'gte:0'),
       // 'max:' . ($this->phase->stage->is_budget_planned ? ($this->phase->stage->remaining_amount + $this->phase->total_cost) : ($this->contract->remaining_amount + $this->phase->total_cost))
      ],
      'taxes' => 'nullable|array',
      'taxes.*' => 'required|array',
      'taxes.*.phase_tax' => 'required|exists:invoice_configs,id',
      'taxes.*.total_tax' => 'required|numeric',
      'taxes.*.is_manual_tax' => 'required|array',
      'taxes.*.is_manual_tax.*' => 'nullable|boolean',
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->contract->end_date,
      'stage_id' => 'required|exists:contract_stages,id,contract_id,' . $this->contract->id, // Ensure the stage id belongs to the contract
    ];

    return $rules;
  }

  public function messages()
  {
    return [
      'estimated_cost.max' => '',
      'due_date.before_or_equal' => 'The due date must be a date before or equal to state due date.',
      'stage_id.exists' => 'Please select a valid stage for this contract.',
      'stage_id.required' => 'The stage field is required. Please select a stage.',
    ];
  }

  // after validation hook
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      foreach ($this->taxes as $index => $tax) {
        $rate = $this->tax_rates->where('id', $tax['phase_tax'])->first();
        // calculate tax amount for each tax and validate it with difference of 1
        $tax_amount = $rate->type == 'Fixed' ? $rate->amount : ($rate->amount * $this->estimated_cost / 100);
        if (abs($this->taxes[$index]['total_tax'] - $tax_amount) > 1) {
          $validator->errors()->add('taxes.' . $index . '.total_tax', 'Tax amount should be between ' . ($tax_amount - 1) . ' and ' . ($tax_amount + 1));
        }

        if ($tax['pay_on_behalf'][0])
          $this->total_tax_amount -= $tax_amount;
        else {
          $this->total_tax_amount += $tax_amount;
        }
      }
    });
  }
}
