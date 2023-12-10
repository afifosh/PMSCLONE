{{-- Stage --}}
{{-- <div class="form-group col-6">
  {{ Form::label('stage_id', __('Stage'), ['class' => 'col-form-label']) }}
  {!! Form::select('stage_id', $stages ?? [], $invoiceItem->invoiceable->stage_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Stage')]) !!}
</div> --}}
{{-- Phase --}}
{{-- <div class="form-group col-6">
  {{ Form::label('phase_id', __('Phase'), ['class' => 'col-form-label']) }}
  {!! Form::select('phase_id', $phases ?? [], $invoiceItem->invoiceable_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Phase')]) !!}
</div> --}}
{{-- Subtotal --}}
{{-- <div class="form-group col-6">
  {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
  {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
</div> --}}

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('name', __('Phase Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', $invoiceItem->invoiceable->name, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label']) }}
    {!! Form::select('stage_id', $stages, $invoiceItem->invoiceable->stage_id, ['class' => 'form-control globalOfSelect2', 'placeholder' => 'Select Stage', 'data-tags' => 'true']) !!}
  </div>
  <div class="form-group col-12">
    {{ Form::label('subtotal', __('Cost'), ['class' => 'col-form-label']) }}
    {!! Form::number('subtotal', $invoiceItem->subtotal, ['class' => 'form-control', 'placeholder' => __('Cost')]) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $invoiceItem->invoiceable->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $invoiceItem->invoiceable->contract->start_date .'", "maxDate":"'. $invoiceItem->invoiceable->contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $invoiceItem->invoiceable->due_date, ['class' => 'form-control flatpickr', 'id' => 'phase_end_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $invoiceItem->invoiceable->contract->start_date .'", "maxDate":"'. $invoiceItem->invoiceable->contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
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

