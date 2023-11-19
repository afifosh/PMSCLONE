
<div class="taxes-section">
  <hr class="hr mt-3" />
    <span class="fw-bold">Tax</span>
  <hr class="hr mt-3" />
  <div class="row">
    {{-- Taxes --}}
    <div class="form-group col-6">
      {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
      <select class="form-select globalOfSelect2" name="item_tax" data-placeholder="{{__('Select Tax')}}">
        @forelse ($tax_rates->where('config_type', 'Tax') as $tax)
          <option @selected(isset($pivot_tax) && $pivot_tax->tax_id == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
            @if($tax->type != 'Percent')
              @cMoney($tax->amount, $invoice->contract->currency, true)
            @else
              {{$tax->amount}}%
            @endif
          )</option>
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
    @if($invoice->type != 'Down Payment' && request()->tab != 'authority-tax')
      {{-- pay_on_behalf --}}
      <div class="col-6 mt-1">
        <label class="switch mt-4">
          {{ Form::checkbox('pay_on_behalf', 1, @$pivot_tax->pay_on_behalf ?? 0, ['class' => 'switch-input'])}}
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
          {{ Form::checkbox('is_authority_tax', 1, @$pivot_tax->is_authority_tax ?? 0, ['class' => 'switch-input'])}}
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label">Authority Tax</span>
        </label>
      </div>
    @endif
  </div>
</div>
