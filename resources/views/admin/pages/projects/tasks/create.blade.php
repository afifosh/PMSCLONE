@if ($task->id)
    {!! Form::model($task, ['route' => ['admin.projects.tasks.update', ['project' => $project, 'task' => $task->id, 'from' => request()->from == 'task-board'? 'task-board' : '']], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($task, ['route' => ['admin.projects.tasks.store',  ['project' => $project, 'from' => request()->from == 'task-board'? 'task-board' : '']], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
      {!! Form::text('subject', null, ['class' => 'form-control', 'placeholder' => __('Subject')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('tags', __('Tags'), ['class' => 'col-form-label']) }}
    {!! Form::select('tags[]', $task->tags ? array_combine($task->tags, $task->tags) : [], null, ['class' => 'form-select globalOfSelect2', 'multiple','data-tags' => 'true']) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $task->start_date, ['class' => 'form-control flatpickr']) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $task->due_date, ['class' => 'form-control flatpickr']) !!}
  </div>

  {{-- priority --}}
  <div class="form-group col-6">
    {{ Form::label('priority', __('Priority'), ['class' => 'col-form-label']) }}
  @php
  $priority = $task::getPossibleEnumValues('priority');
  @endphp
    {!! Form::select('priority', array_combine($priority, $priority), $task->priority, ['class' => 'form-select globalOfSelect2']) !!}
  </div>

  {{-- status --}}
  <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
  @php
  $enum = $task::getPossibleEnumValues('status');
  @endphp
    {!! Form::select('status', array_combine($enum, $enum), $task->status, ['class' => 'form-select globalOfSelect2']) !!}
  </div>

  {{-- Assignee --}}
  @php
    $optionParameters = collect($admins)->mapWithKeys(function ($item) {
        return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
    })->all();
  @endphp

  <div class="form-group">
    {{ Form::label('assignees[]', __('Assignees'), ['class' => 'col-form-label']) }}
    {!! Form::select('assignees[]', $admins->pluck('email', 'id'), null, ['class' => 'form-select globalOfSelect2User', 'data-placeholder' => 'Select Assignees', 'multiple' => 'multiple'], $optionParameters) !!}
  </div>

  {{-- Followers --}}
  <div class="form-group">
    {{ Form::label('followers[]', __('Followers'), ['class' => 'col-form-label']) }}
    {!! Form::select('followers[]', $admins->pluck('email', 'id'), null, ['class' => 'form-select globalOfSelect2User', 'data-placeholder' => 'Select Followers', 'multiple' => 'multiple'], $optionParameters) !!}
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
