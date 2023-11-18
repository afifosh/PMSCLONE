<div class="row bg-light rounded p-2 position-relative mb-2" data-repeater-item>
  <button type="button" class="position-absolute top-0 end-0 btn btn-icon btn-primary" data-repeater-delete> <i class="fa fa-trash"></i> </button>
  {{-- taxes --}}
  <div class="col-6">
    {{ Form::label('phase_taxes', __('Tax'), ['class' => 'col-form-label']) }}
    <select class="form-select tax-rate" name="taxes[{{$index}}][phase_tax]" data-placeholder="{{__('Select Tax')}}" data-allow-clear=true>
      @forelse ($tax_rates as $tax_rate)
        <option @selected(@$tax->id == $tax_rate->id) value="{{$tax_rate->id}}" data-amount="{{$tax_rate->amount}}" data-type={{$tax_rate->type}}>{{$tax_rate->name}} (
          @if($tax_rate->type != 'Percent')
            @cMoney($tax_rate->amount, $phase->contract->currency, true)
          @else
            {{$tax_rate->amount}}%
          @endif
        )</option>
      @empty
      @endforelse
    </select>
  </div>
  {{-- total Tax Value --}}
  <div class="form-group col-6">
    {{ Form::label('total_tax', __('Tax Value'), ['class' => 'col-form-label']) }}
    {!! Form::number('taxes['. $index .'][total_tax]', @$tax->pivot->manual_amount ? @$tax->pivot->manual_amount / 1000 : @$tax->pivot->calculated_amount / 1000, ['class' => 'form-control tax-amount', 'placeholder' => __('Tax Value'), 'disabled'])!!}
  </div>
  {{-- is Mantual Tax --}}
  <div class="col-6 mt-1">
    <label class="switch mt-4">
      {{ Form::checkbox('taxes['. $index .'][is_manual_tax]', 1, @$tax->pivot->manual_amount != 0,['class' => 'switch-input is-manual-tax'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Adjust Tax</span>
    </label>
  </div>
  {{-- pay on behalf --}}
  <div class="col-6 mt-1">
    <label class="switch mt-4">
      {{ Form::checkbox('taxes['. $index .'][pay_on_behalf]', 1, @$tax->pivot->pay_on_behalf, ['class' => 'switch-input pay-on-behalf'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Pay On Behalf</span>
    </label>
  </div>
  {{-- is_authority_tax --}}
  <div class="col-6 mt-1">
    <label class="switch mt-4">
      {{ Form::checkbox('taxes['. $index .'][is_authority_tax]', 1, @$tax->pivot->is_authority_tax, [
        'class' => 'switch-input is-authority-tax',
        'disabled' => @$tax->pivot->pay_on_behalf ? true : false
        ])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Authority Tax</span>
    </label>
  </div>
  {{-- End is Mantual Tax --}}
</div>
