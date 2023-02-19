@if ($programUser->id)
    {!! Form::model($program, ['route' => ['admin.programs.users.update', ['program' => $program->id, 'user' => $programUser->id]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($program, ['route' => ['admin.programs.users.store', $program->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
@php
  $optionParameters = collect($users)->mapWithKeys(function ($item) {
      return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
  })->all();
@endphp

<div class="form-group">
  {{ Form::label('users[]', __('User'), ['class' => 'col-form-label']) }}
  {!! Form::select('users[]', $users->pluck('email', 'id'), '', ['class' => 'form-select globalOfSelect2User', 'data-placeholder' => 'Select Users', 'multiple' => 'multiple'], $optionParameters) !!}
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
