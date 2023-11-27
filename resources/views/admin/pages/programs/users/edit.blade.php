@if ($programUser->id)
    {!! Form::model($program, ['route' => ['admin.programs.users.update', ['program' => $program->id, 'user' => $programUser->admin_id]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($program, ['route' => ['admin.programs.users.store', $program->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
@php
  $optionParameters = collect($users)->mapWithKeys(function ($item) {
      return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
  })->all();
@endphp

<div class="form-group mb-3 col-sm-12">
    {{ Form::label('user', __('User'), ['class' => 'col-form-label']) }}
    {!! Form::select('user', $users->pluck('email', 'id'), $programUser->admin_id, ['class' => 'form-select globalOfSelect2User', 'data-placeholder' => 'Select User', 'data-allow-clear' => 'true'], $optionParameters) !!}
</div>
<div class="form-group form-check mb-3 form-switch col-sm-12">
      <?php echo e(Form::checkbox('permanent_access', 1, $programUser->permanent_access, ['class' => 'form-check-input', 'id' => 'permanent_access'])); ?>
      <?php echo e(Form::label('permanent_access', __('Does this user have permanent access?'), ['class' => 'form-check-label'])); ?>
</div>
<div class="form-group col-sm-12 {{ $programUser->permanent_access ? 'd-none' : '' }}" id="date_input">
    <label for="until_at">Access Until:</label>
    {!! Form::text('until_at',  $programUser->until_at, ['id' => 'until_at','class' => 'form-control flatpickr', 'data-flatpickr' => '{"minDate":"today","enableTime":false, "dateFormat": "Y-m-d",  "allowInput": false}',  'required'=> 'true']) !!}
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
