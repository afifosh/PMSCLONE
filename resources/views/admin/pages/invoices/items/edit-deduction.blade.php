<div class="deduction-section">
  <hr class="hr mt-3" />
    <span class="fw-bold">Deduction</span>
  <hr class="hr mt-3" />
  <div class="row">
    {{-- is_before_tax --}}
    <div class="form-group col-6">
      {{ Form::label('is_before_tax', __('Deduction Calculation'), ['class' => 'col-form-label']) }}
      {!! Form::select('is_before_tax', ['1' => 'Excluding Tax', '0' => 'Including Tax'], @$invoiceItem->deduction->is_before_tax ? 1 : 0, ['class' => 'form-select globalOfSelect2', 'id' => 'is_before_tax']) !!}
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
    {{-- is_fixed_amount --}}
    <div class="col-6 mt-3">
      <label class="switch mt-4">
        {{ Form::checkbox('is_fixed_amount', 1, @$invoiceItem->deduction->dp_rate_id ? 0 : 1,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Use Fixed Amount</span>
      </label>
    </div>
    {{-- Downpayment Rates --}}
    <div class="form-group col-6 cal-deduction-section {{@$invoiceItem->deduction->dp_rate_id ? '' : 'd-none'}}">
      {{ Form::label('dp_rate_id', __('Down payment Rate'), ['class' => 'col-form-label']) }}
      <select class="form-select globalOfSelect2" name="dp_rate_id" data-allow-clear='true' data-placeholder="{{__('Select Deduction Rate')}}">
        <option value="">{{__('Select Deduction Rate')}}</option>
        @forelse ($deduction_rates->where('config_type', 'Down Payment') as $tax)
          <option @selected(@$invoiceItem->deduction->dp_rate_id == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
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
    <div class="form-group col-6 cal-deduction-section {{@$invoiceItem->deduction->dp_rate_id ? '' : 'd-none'}}">
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
      {!! Form::number('downpayment_amount', @$invoiceItem->deduction->manual_amount ? @$invoiceItem->deduction->manual_amount : ($invoiceItem->deduction->amount ?? 0), [
        'class' => 'form-control',
        'disabled' => @$invoiceItem->deduction->dp_rate_id ? true : false,
        'placeholder' => __('Downpayment Amount')
      ]) !!}
    </div>
    {{-- Adjust Deduction --}}
    <div class="col-6 mt-3 cal-deduction-section {{@$invoiceItem->deduction->dp_rate_id ? '' : 'd-none'}}">
      <label class="switch mt-4">
        {{ Form::checkbox('is_manual_deduction', 1, ($invoiceItem->deduction->manual_amount ?? 0) && 1,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
        <span class="switch-label">Adjust Deduction Amount</span>
      </label>
    </div>
  </div>
</div>
