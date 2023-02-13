@if ($programUser->id)
    {!! Form::model($program, ['route' => ['admin.programs.users.update', ['program' => $program->id, 'user' => $programUser->id]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($program, ['route' => ['admin.programs.users.store', $program->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="form-group">
  {{ Form::label('users[]', __('User'), ['class' => 'col-form-label']) }}
  {!! Form::select('users[]', $users, '', ['class' => 'form-select globalOfSelect2', 'multiple' => 'multiple']) !!}
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
