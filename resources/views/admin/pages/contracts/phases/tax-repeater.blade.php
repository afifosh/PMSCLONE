<div class="row bg-light rounded p-2 position-relative mb-2" data-repeater-item data-keep-item="{{isset($tax) ? 1 : 0}}" style="{{!isset($tax) ? 'display:none;' : ''}}">
  <button type="button" class="position-absolute top-0 end-0 btn btn-icon btn-primary" data-repeater-delete> <i class="fa fa-trash"></i> </button>
  {{-- taxes --}}
  <div class="col-6">
    {{ Form::label('phase_taxes', __('Tax'), ['class' => 'col-form-label']) }}
    <select class="form-select taxesSelect globalOfSelect2 tax-rate" name="taxes[{{$index}}][phase_tax]" data-placeholder="{{__('Select Tax')}}" data-allow-clear=true>
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
          <option @selected(@$tax->id == $tax_rate->id) value="{{$tax_rate->id}}" data-amount="{{$tax_rate->amount}}" data-type={{$tax_rate->type}} data-category="{{$tax_rate->category}}">
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
    {!! Form::number('taxes['. $index .'][total_tax]', @$tax->pivot->manual_amount ? @$tax->pivot->manual_amount / 100 : @$tax->pivot->calculated_amount / 100, ['class' => 'form-control tax-amount', 'placeholder' => __('Tax Value'), 'disabled' => @$tax->pivot->manual_amount != 0 ? false : true ])!!}
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
  {{-- End is Mantual Tax --}}
</div>
