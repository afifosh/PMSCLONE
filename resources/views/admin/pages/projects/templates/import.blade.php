{!! Form::open(['route' => ['admin.projects.import-templates.store', ['project' => $project->id]], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}

<div class="row">
  <div class="form-group">
      {{ Form::label('name', __('Select Template'), ['class' => 'col-form-label']) }}
      {!! Form::select('template', $templates, null, ['class' => 'form-select globalOfSelect2']) !!}
  </div>


<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
