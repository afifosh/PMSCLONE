@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.update', ['project' => 'project', 'contract' => $contract, 'phase' => $phase->id, 'stage' => $stage]],
      'method' => 'PUT',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.store',  ['project' => 'project', 'contract' => $contract, 'stage' => $stage]], 'method' => 'POST']) !!}
@endif
<div class="row rr-single">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label']) }}
    @php
    $selectedStageId = isset($stage->id) ? $stage->id : null;
    @endphp
    {!! Form::select('stage_id', $stages, $selectedStageId, ['class' => 'form-control select2', 'placeholder' => 'Select Stage']) !!}

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
  {{-- taxes --}}
  <div class="col-6">
    {{ Form::label('phase_taxes', __('Tax'), ['class' => 'col-form-label']) }}
    <select class="form-select globalOfSelect2" name="phase_taxes[]" multiple data-placeholder="{{__('Select Tax')}}" data-allow-clear=true>
      @forelse ($tax_rates->where('is_retention', false) as $tax)
        <option @selected($phase->taxes->contains($tax)) value="{{$tax->id}}" data-amount="{{$tax->amount}}" data-type={{$tax->type}}>{{$tax->name}} (
          @if($tax->type != 'Percent')
            @cMoney($tax->amount, $phase->contract->currency, true)
          @else
            {{$tax->amount}}%
          @endif
        )</option>
      @empty
      @endforelse
    </select>
  </div>
  {{-- total Tax Value --}}
  <div class="form-group col-6">
    {{ Form::label('total_tax', __('Tax Value'), ['class' => 'col-form-label']) }}
    {!! Form::number('total_tax', $phase->tax_amount, ['class' => 'form-control', 'placeholder' => __('Tax Value'), 'disabled'])!!}
  </div>
  {{-- is Mantual Tax --}}
  <div class="col-6 mt-1">
    <label class="switch mt-4">
      {{ Form::checkbox('is_manual_tax', 1, $phase->manual_tax_amount != 0,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is Manual Tax?</span>
    </label>
  </div>
  {{-- End is Mantual Tax --}}
  <!-- Manual Tax field -->
  <div class="form-group col-6 {{$phase->manual_tax_amount != 0 ? '' : 'd-none'}}">
    {{ Form::label('manual_tax_amount', __('Manual Tax Amount'), ['class' => 'col-form-label']) }}
    {!! Form::number('manual_tax_amount', null, ['class' => 'form-control', 'placeholder' => __('Manual Tax Amount')]) !!}
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
