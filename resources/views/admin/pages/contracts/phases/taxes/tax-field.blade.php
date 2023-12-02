<div class="row rounded p-2 mb-2">
  {{-- taxes --}}
  <div class="col-6">
    {{ Form::label('phase_taxes', __('Tax'), ['class' => 'col-form-label']) }}
    <select class="form-select taxesSelect globalOfSelect2 tax-rate" name="tax" data-placeholder="{{__('Select Tax')}}" data-allow-clear=true>
      @php
          $optGroup = '';
      @endphp
      @forelse ($tax_rates->where('config_type', 'Tax') as $tax_rate)
        @if($optGroup != $tax_rate->category)
          @php
              $optGroup = $tax_rate->category;
          @endphp
          <optgroup label="{{$tax_rate->categoryName()}}">
        @endif
          <option @selected(@$tax->tax_id == $tax_rate->id) value="{{$tax_rate->id}}" data-amount="{{$tax_rate->amount}}" data-type={{$tax_rate->type}} data-category="{{$tax_rate->category}}">
            {{$tax_rate->name}}
            (
              @if($tax_rate->type != 'Percent')
                @cMoney($tax_rate->amount, $phase->contract->currency, true)
              @else
                {{$tax_rate->amount}}%
              @endif
            )
          </option>
        @if($optGroup != $tax_rate->category)
          </optgroup>
        @endif
      @empty
      @endforelse
    </select>
  </div>
  {{-- total Tax Value --}}
  <div class="form-group col-6">
    {{ Form::label('total_tax', __('Tax Value'), ['class' => 'col-form-label']) }}
    {!! Form::number('total_tax', @$tax->manual_amount ? @$tax->manual_amount : @$tax->calculated_amount, ['class' => 'form-control tax-amount', 'placeholder' => __('Tax Value'), 'disabled' => @$tax->manual_amount != 0 ? false : true ])!!}
  </div>
  {{-- is Mantual Tax --}}
  <div class="col-6 mt-1">
    <label class="switch mt-4">
      {{ Form::checkbox('is_manual_tax', 1, @$tax->manual_amount != 0,['class' => 'switch-input is-manual-tax'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Adjust Tax</span>
    </label>
  </div>
  {{-- End is Mantual Tax --}}
</div>
