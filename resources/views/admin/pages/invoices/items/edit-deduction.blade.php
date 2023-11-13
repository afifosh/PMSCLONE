<div class="deduction-section">
  <hr class="hr mt-3" />
  {{-- Deduct Downpayment --}}
  <div class="">
    <label class="switch">
      <span class="switch-label fw-bold">Deduct Down Payment?</span>
      {{ Form::checkbox('deduct_downpayment', 1, @$invoiceItem->deduction->id && 1,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
    </label>
  </div>
  <div class="row {{($invoiceItem->deduction->id ?? 0) ? '' : 'd-none'}}">
    {{-- is_before_tax --}}
    <div class="form-group col-6">
      {{ Form::label('is_before_tax', __('Before Tax'), ['class' => 'col-form-label']) }}
      {!! Form::select('is_before_tax', ['1' => 'Yes', '0' => 'No'], @$invoiceItem->deduction->is_before_tax ? 1 : 0, ['class' => 'form-select globalOfSelect2', 'id' => 'is_before_tax']) !!}
    </div>
    {{-- Downpayment Deduction --}}
    <div class="form-group col-6">
      {{ Form::label('downpayment_id', __(' Down payment'), ['class' => 'col-form-label']) }}
      <select name="downpayment_id" id="downpayment_id" class="form-select globalOfSelect2">
        <option value="">{{__('Select Down payment')}}</option>
        @forelse ($invoice->deductableDownpayments as $dp)
          <option data-amount="{{$dp->total}}" value="{{$dp->id}}" @selected($invoiceItem->deduction->downpayment_id ?? 0 == $dp->id)>{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $invoice->contract->currency, true) )</option>
        @empty
        @endforelse
      </select>
    </div>
    <div class="col-12 mt-3 downpayment-info">
    </div>
    {{-- Downpayment Rates --}}
    <div class="form-group col-6">
      {{ Form::label('dp_rate_id', __('Down payment Rate'), ['class' => 'col-form-label']) }}
      <select class="form-select globalOfSelect2" name="dp_rate_id" data-allow-clear='true' data-placeholder="{{__('Select Deduction Rate')}}">
        <option value="">{{__('Select Deduction Rate')}}</option>
        @forelse ($tax_rates->where('config_type', 'Down Payment') as $tax)
          <option @selected($invoiceItem->deduction->dp_rate_id ?? 0 == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
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
    {{-- calculation_source --}}
    <div class="form-group col-6">
      {{ Form::label('calculation_source', __('Percentage Calculation Source'), ['class' => 'col-form-label']) }}
      {!! Form::select('calculation_source', ['Down Payment' => 'Down Payment Total', 'Deductible' => 'Item Total'], $invoiceItem->deduction->calculation_source ??null, [
        'class' => 'form-control globalOfSelect2',
        'data-placeholder' => __('Calculation Source'),
        'data-allow-clear' => 'true',
      ])!!}
    </div>
    {{-- Downpayment Deduction --}}
    <div class="form-group col-6">
      {{ Form::label('downpayment_amount', __('Downpayment Amount'), ['class' => 'col-form-label']) }}
      {!! Form::number('downpayment_amount', $invoiceItem->deduction->amount ?? 1, ['class' => 'form-control', 'disabled', 'placeholder' => __('Downpayment Amount')]) !!}
    </div>
    {{-- Adjust Deduction --}}
    <div class="col-6 mt-3">
      <label class="switch mt-4">
        {{ Form::checkbox('is_manual_deduction', 1, ($invoiceItem->deduction->manual_amount ?? 0) && 1,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Adjust Deduction Amount</span>
      </label>
    </div>
    {{-- Adjusted Deduction Amount --}}
    <div class="form-group col-6 {{($invoiceItem->deduction->manual_amount ?? 0) ? '' : 'd-none'}}">
      {{ Form::label('manual_deduction_amount', __('Adjusted Deduction Amount'), ['class' => 'col-form-label']) }}
      {!! Form::number('manual_deduction_amount', $invoiceItem->deduction->manual_amount ?? 0, ['class' => 'form-control', 'placeholder' => __('Adjusted Deduction Amount')]) !!}
    </div>
  </div>
</div>
