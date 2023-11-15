
<div class="taxes-section">
  <hr class="hr mt-3" />
    <span class="fw-bold">Tax</span>
  <hr class="hr mt-3" />
  {{-- <div class="">
    <label class="switch">
      <span class="switch-label fw-bold">Add Tax?</span>
      {{ Form::checkbox('add_tax', 1, count($invoiceItem->taxes ?? []) && 1,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
    </label>
  </div> --}}
  <div class="row">
    {{-- Taxes --}}
    <div class="form-group col-6">
      {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
      <select class="form-select globalOfSelect2" name="item_tax" data-placeholder="{{__('Select Tax')}}">
        @forelse ($tax_rates->where('config_type', 'Tax') as $tax)
          <option @selected(@$invoiceItem->id && $invoiceItem->taxes->contains($tax->id)) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
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
      {!! Form::number('total_tax_amount', $invoiceItem->total_tax_amount ?? 0, ['class' => 'form-control', 'disabled', 'placeholder' => __('Tax Value')]) !!}
    </div>
    {{-- Adjust Tax --}}
    <div class="col-6 mt-1">
      <label class="switch mt-4">
        {{ Form::checkbox('is_manual_tax', 1, @$invoiceItem->id && $invoiceItem->manual_tax_amount > 0 ? 1 : 0,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Adjust tax</span>
      </label>
    </div>
    {{-- Adjusted Tax --}}
    {{-- <div class="form-group col-6 {{@$invoiceItem->id && $invoiceItem->manual_tax_amount > 0 ? '' : 'd-none'}}">
      {{ Form::label('manual_tax_amount', __('Adjusted Tax'), ['class' => 'col-form-label']) }}
      {!! Form::number('manual_tax_amount', $invoiceItem->manual_tax_amount ?? 0, ['class' => 'form-control', 'placeholder' => __('Adjusted Tax')]) !!}
    </div> --}}

    {{-- pay_on_behalf --}}
    <div class="col-6 mt-1">
      <label class="switch mt-4">
        {{ Form::checkbox('pay_on_behalf', 1, 0, ['class' => 'switch-input'])}}
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
        {{ Form::checkbox('is_authority_tax', 1, 0, ['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Authority Tax</span>
      </label>
    </div>
  </div>
</div>
