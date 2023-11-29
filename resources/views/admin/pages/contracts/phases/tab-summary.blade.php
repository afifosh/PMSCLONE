@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.update', ['project' => 'project', 'contract' => $contract, 'phase' => $phase->id, 'stage' => $stage, 'tableId' => request()->tableId]],
      'method' => 'PUT',
      'class' => 'phase-create-form',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.store',  ['project' => 'project', 'contract' => $contract, 'stage' => $stage, 'tableId' => request()->tableId]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
@endif
<div class="row rr-single repeater">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label']) }}
    @php
    $selectedStageId = isset($stage->id) ? $stage->id : null;
    @endphp
    {!! Form::select('stage_id', $stages, $selectedStageId, ['class' => 'form-control globalOfSelect2', 'placeholder' => 'Select Stage']) !!}

  </div>
  <div class="form-group col-12">
    {{ Form::label('estimated_cost', __('Estimated Cost'), ['class' => 'col-form-label']) }}
    <div class="dropdown open d-inline">
      <span data-bs-toggle="dropdown" aria-haspopup="true">
          <i class="fas fa-calculator"></i>
      </span>
      <div class="dropdown-menu p-3">
        <div class="mb-3" data-content="percent-cal">
          <label for="percent-value" class="form-label">Percentage (Total: {{$contract->value}})</label>
          <input type="number" name="percent-value" id="percent-value" data-balance="{{$contract->value}}" class="form-control" placeholder="10%">
        </div>
      </div>
    </div>
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>
  <div class="col-12 rounded mt-3 taxes-section" data-repeater-list="taxes">
    <hr>
    <div class="d-flex">
      <span class="pt-1 me-2">Phase Tax</span>
      <button type="button" class="btn btn-sm btn-primary" data-repeater-create>
          <i class="fas fa-plus-circle"></i>
      </button>
    </div>
    <hr>
    @forelse ($phase->taxes as $tax)
      @include('admin.pages.contracts.phases.tax-repeater', ['tax' => $tax, 'index' => $loop->index])
    @empty
      @include('admin.pages.contracts.phases.tax-repeater', ['index' => 0])
    @endforelse
  </div>
  {{-- deduction section --}}
  <div class="deduction-section">
    <hr class="hr mt-3" />
      <label class="switch mt-1">
        <span class="fw-bold switch-label">Deduction</span>
        {{ Form::checkbox('add_deduction', 1, @$phase->deduction ? 1 : 0,['class' => 'switch-input'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
      </label>
    <div class="row deduction-inputs {{$phase->deduction ? '' : 'd-none'}}">
      <div>
        <hr class="hr mt-3" />
      </div>

      {{-- is_before_tax --}}
      <div class="form-group col-6">
        {{ Form::label('is_before_tax', __('Deduction Calculation'), ['class' => 'col-form-label']) }}
        {!! Form::select('is_before_tax', ['1' => 'Excluding Tax', '0' => 'Including Tax'], @$phase->deduction->is_before_tax ? 1 : 0, ['class' => 'form-select globalOfSelect2', 'id' => 'is_before_tax']) !!}
      </div>
      {{-- Downpayment Deduction --}}
      <div class="form-group col-6">
        {{ Form::label('downpayment_id', __(' Down payment'), ['class' => 'col-form-label']) }}
        <select name="downpayment_id" id="downpayment_id" class="form-select globalOfSelect2">
          <option value="">{{__('Select Down payment')}}</option>
          @forelse ($contract->deductableDownpayments as $dp)
            <option data-amount="{{$dp->total}}" value="{{$dp->id}}" @selected($phase->deduction->downpayment_id ?? 0 == $dp->id)>{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $contract->currency, true) )</option>
          @empty
          @endforelse
        </select>
      </div>
      <div class="col-12 mt-3 downpayment-info">
      </div>
      {{-- is_fixed_amount --}}
      <div class="col-6 mt-3">
        <label class="switch mt-4">
          {{ Form::checkbox('is_fixed_amount', 1, @$phase->deduction->dp_rate_id ? 0 : 1,['class' => 'switch-input'])}}
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label">Use Fixed Amount</span>
        </label>
      </div>
      {{-- Downpayment Rates --}}
      <div class="form-group col-6 cal-deduction-section {{@$phase->deduction->dp_rate_id ? '' : 'd-none'}}">
        {{ Form::label('dp_rate_id', __('Down payment Rate'), ['class' => 'col-form-label']) }}
        <select class="form-select globalOfSelect2" name="dp_rate_id" data-allow-clear='true' data-placeholder="{{__('Select Deduction Rate')}}">
          <option value="">{{__('Select Deduction Rate')}}</option>
          @forelse ($tax_rates->where('config_type', 'Down Payment') as $tax)
            <option @selected(@$phase->deduction->dp_rate_id == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
              @if($tax->type != 'Percent')
                @cMoney($tax->amount, $contract->currency, true)
              @else
                {{$tax->amount}}%
              @endif
            )</option>
          @empty
          @endforelse
        </select>
      </div>
      {{-- calculation_source --}}
      <div class="form-group col-6 cal-deduction-section {{@$phase->deduction->dp_rate_id ? '' : 'd-none'}}">
        {{ Form::label('calculation_source', __('Percentage Calculation Source'), ['class' => 'col-form-label']) }}
        {!! Form::select('calculation_source', ['Down Payment' => 'Down Payment Total', 'Deductible' => 'Item Total'], $phase->deduction->calculation_source ??null, [
          'class' => 'form-control globalOfSelect2',
          'data-placeholder' => __('Calculation Source'),
          'data-allow-clear' => 'true',
        ])!!}
      </div>
      {{-- Downpayment Deduction --}}
      <div class="form-group col-6">
        {{ Form::label('downpayment_amount', __('Downpayment Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('downpayment_amount', @$phase->deduction->manual_amount ? @$phase->deduction->manual_amount : ($phase->deduction->amount ?? 0), [
          'class' => 'form-control',
          'disabled' => @$phase->deduction->dp_rate_id ? true : false,
          'placeholder' => __('Downpayment Amount')
        ]) !!}
      </div>
      {{-- Adjust Deduction --}}
      <div class="col-6 cal-deduction-section {{@$phase->deduction->dp_rate_id ? '' : 'd-none'}}">
        <label class="switch mt-3">
          {{ Form::checkbox('is_manual_deduction', 1, ($phase->deduction->manual_amount ?? 0) && 1,['class' => 'switch-input'])}}
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label">Adjust Deduction Amount</span>
        </label>
      </div>
    </div>
  </div>
  <div>
    <hr class="mt-2">
  </div>

  {{-- total cost --}}
  <div class="form-group col-6">
    {{ Form::label('total_cost', __('Total Cost'), ['class' => 'col-form-label']) }}
    {!! Form::number('total_cost', null, ['class' => 'form-control', 'placeholder' => __('Total Cost'), 'disabled', 'data-max' => $max_amount])!!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $phase->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $contract->start_date .'", "maxDate":"'. $contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $phase->due_date, ['class' => 'form-control flatpickr', 'id' => 'phase_end_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $contract->start_date .'", "maxDate":"'. $contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
  </div>
  <div class="col-12 mt-2">
    <div class="form-check">
      <input class="form-check-input" name="calc_end_date" type="checkbox" id="cal-phase-end-date">
      <label class="form-check-label" for="cal-phase-end-date">
        Calculate End Date
      </label>
    </div>
  </div>
  <div class="d-none" id="end-date-cal-form">
    <hr>
    <div class="mb-3">
      <label for="" class="form-label">After From Start Date</label>
      <div class="d-flex">
        <div class="d-flex w-100">
          <input id="cont-add-count" type="number"  class="form-control cal-phase-end-date">
          {!! Form::select('cal_end_date_unit', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], null, ['class' => 'cont-add-unit cal-phase-end-date input-group-text form-select globalOfSelect2']) !!}
        </div>
      </div>
    </div>
    <hr>
  </div>

</div>
<div class="col-md-12 mt-3">
  <div class="mb-3">
    {!! Form::label('description', 'Description', ['class' => 'col-form-label']) !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) !!}
  </div>
</div>

<div class="mt-3 d-flex justify-content-between">
  <div class="phase-editing-users">
  </div>
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
