@if ($task->id)
    {!! Form::model($task, ['route' => ['admin.project-templates.tasks.update', ['project_template' => $project_template->id, 'task' => $task->id]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($task, ['route' => ['admin.project-templates.tasks.store',['project_template' => $project_template->id]], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
  <div class="col-6">
    <div class="form-group">
        {{ Form::label('subject', __('Title'), ['class' => 'col-form-label']) }}
        {!! Form::text('subject', null, ['class' => 'form-control', 'placeholder' => __('Title')]) !!}
    </div>
  </div>
  {{-- priority --}}
  <div class="form-group col-6">
    {{ Form::label('priority', __('Priority'), ['class' => 'col-form-label']) }}
  @php
  $priority = $task::getPossibleEnumValues('priority');
  @endphp
    {!! Form::select('priority', array_combine($priority, $priority), $task->priority, ['class' => 'form-select globalOfSelect2']) !!}
  </div>

  <div class="form-group col-12">
    {{ Form::label('tags', __('Tags'), ['class' => 'col-form-label']) }}
    {!! Form::select('tags[]', $task->tags ? array_combine($task->tags, $task->tags) : [], null, ['class' => 'form-select globalOfSelect2', 'multiple','data-tags' => 'true']) !!}
  </div>

  <div class="col-12">
    <div class="form-group">
        {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('Description')]) !!}
    </div>
  </div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
