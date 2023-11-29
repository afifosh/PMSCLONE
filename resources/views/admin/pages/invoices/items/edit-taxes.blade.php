
<div class="taxes-section">
  <hr class="hr mt-3" />
    <span class="fw-bold">Tax</span>
  <hr class="hr mt-3" />
  <div class="row">
    {{-- Taxes --}}
    <div class="form-group col-6">
      {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
      <select class="form-select globalOfSelect2" name="item_tax" data-placeholder="{{__('Select Tax')}}">
        @php
          $optGroup = '';
        @endphp
        @forelse ($tax_rates->where('config_type', 'Tax') as $tax)
        {{-- opt group --}}
          @if($optGroup != $tax->category)
            @php
                $optGroup = $tax->category;
            @endphp
            <optgroup label="{{$tax->categoryName()}}">
          @endif

          {{-- Option --}}
          <option @selected(isset($pivot_tax) && $pivot_tax->tax_id == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}" data-category="{{$tax->category}}">
            {{$tax->name}}
            (
              @if($tax->type != 'Percent')
                @cMoney($tax->amount, $invoice->contract->currency, true)
              @else
                {{$tax->amount}}%
              @endif
            )
          </option>

          {{-- close opt group --}}
          @if($optGroup != $tax->category)
          </optgroup>
          @endif
        @empty
        @endforelse
      </select>
    </div>
    {{-- Tax Value --}}
    <div class="form-group col-6">
      {{ Form::label('total_tax_amount', __('Tax Value'), ['class' => 'col-form-label']) }}
      {!! Form::number('total_tax_amount', @$pivot_tax->manual_amount ? $pivot_tax->manual_amount :  @$pivot_tax->calculated_amount ?? 0, [
        'class' => 'form-control',
        'disabled' => @$pivot_tax->manual_amount ? false : true,
        'placeholder' => __('Tax Value')
      ]) !!}
    </div>
    {{-- Adjust Tax --}}
    <div class="col-6 mt-1">
      <label class="switch mt-4">
        {{ Form::checkbox('is_manual_tax', 1, @$pivot_tax->manual_amount > 0 ? 1 : 0,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Adjust tax</span>
      </label>
    </div>
  </div>
</div>
