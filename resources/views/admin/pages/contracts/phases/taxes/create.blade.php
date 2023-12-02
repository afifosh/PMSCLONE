@if ($tax->id)
    {!! Form::model($phase, ['route' => ['admin.contracts.phases.taxes.update', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'tax' => $tax->id]],
      'method' => 'PUT',
      'class' => 'phase-create-form',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.contracts.phases.taxes.store',  ['contract' => $phase->contract_id, 'phase' => $phase->id,]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
@endif
<div class="row rr-single repeater">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name'), 'disabled']) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label', 'disabled']) }}
    @php
    $selectedStageId = isset($stage->id) ? $stage->id : null;
    @endphp
    {!! Form::select('stage_id', $stages, $selectedStageId, ['class' => 'form-control globalOfSelect2', 'disabled']) !!}

  </div>
  <div class="form-group col-12">
    {{ Form::label('estimated_cost', __('Cost'), ['class' => 'col-form-label']) }}
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'disabled', 'placeholder' => __('Cost')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $phase->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'disabled', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $phase->due_date, ['class' => 'form-control flatpickr', 'id' => 'phase_end_date', 'disabled', 'placeholder' => __('Due Date')]) !!}
  </div>
</div>
@include('admin.pages.contracts.phases.taxes.tax-field')

<div class="mt-3 d-flex justify-content-between">
  <div class="phase-editing-users">
  </div>
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
