@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.phases.update', ['project' => $project, 'phase' => $phase->id]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.phases.store',  ['project' => $project]], 'method' => 'POST']) !!}
@endif

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('duration', __('Duration'), ['class' => 'col-form-label']) }}
    {!! Form::date('duration', null, ['class' => 'form-control flatpickr', 'data-flatpickr' => $phase->id ? '{"mode": "range", "dateFormat": "Y-m-d", "defaultDate": ["'.$phase->start_date->toDateString().'", "'.$phase->due_date->toDateString().'"]}' : '{"mode": "range", "dateFormat": "Y-m-d"}', 'placeholder' => __('Duration')]) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('estimated_cost', __('Estimated Cost'), ['class' => 'col-form-label']) }}
    {!! Form::text('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>

  {{-- status --}}
  <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    {!! Form::select('status', array_combine($phase_statuses, $phase_statuses), $phase->status, ['class' => 'form-select globalOfSelect2']) !!}
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
