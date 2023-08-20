@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.phases.update', ['project' => $project, 'contract' => $contract, 'phase' => $phase->id]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.phases.store',  ['project' => $project, 'contract' => $contract]], 'method' => 'POST']) !!}
@endif

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('estimated_cost', __('Estimated Cost'), ['class' => 'col-form-label']) }}
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $phase->start_date, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'.$contract->start_date.'", "maxDate":"'.$contract->end_date.'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $phase->due_date, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'.$contract->start_date.'", "maxDate":"'.$contract->end_date.'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
  </div>

</div>
<div class="col-md-12 mt-3">
  <div class="mb-3">
    {!! Form::label('description', 'Description', ['class' => 'col-form-label']) !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) !!}
  </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
