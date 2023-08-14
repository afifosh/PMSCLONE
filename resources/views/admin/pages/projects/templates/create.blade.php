@if ($template->id)
    {!! Form::model($template, ['route' => ['admin.project-templates.update', ['project_template' => $template->id, 'type' => request()->type]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($template, ['route' => ['admin.project-templates.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
  <div class="form-group">
      {{ Form::label('name', __('Template Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>

  {!! Form::hidden('tasks[]', null, ['id' => 'tasks_ids', 'placeholder' => __('Name')]) !!}

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" data-preAjaxAction="includeTasks" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
