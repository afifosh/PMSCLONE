@if ($checkItem->id)
    {!! Form::model($checkItem, ['route' => ['admin.project-templates.tasks.check-items.update', ['project_template' => $task->project_template_id, 'task' => $task->id, 'check_item' => $checkItem->id]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($checkItem, ['route' => ['admin.project-templates.tasks.check-items.store',['project_template' => $task->project_template_id, 'task' => $task->id]], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
  <div class="form-group">
      {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
      {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Title')]) !!}
  </div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
