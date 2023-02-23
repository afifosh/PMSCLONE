@if ($share->id)
    {!! Form::model($share, ['route' => ['admin.draft-rfps.files.shares.reinvite', ['draft_rfp' => $share->id, 'file' => $share->rfp_file_id ,'share' => $share->id]], 'method' => 'POST']) !!}
@else
    {!! Form::model($share, ['route' => ['admin.draft-rfps.files.shares.store', ['draft_rfp' => $file->rfp_id, 'file' => $file]], 'method' => 'POST']) !!}
@endif
@php
  $optionParameters = collect($users)->mapWithKeys(function ($item) {
      return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
  })->all();
@endphp

<div class="row">
  <div class="form-group">
    {{ Form::label('users[]', __('User'), ['class' => 'col-form-label']) }}
    {!! Form::select('users[]', $users->pluck('email', 'id'), '', ['class' => 'form-select globalOfSelect2User', 'data-placeholder' => 'Select Users', 'multiple' => 'multiple'], $optionParameters) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('expires_at', __('Invitation Expiry'), ['class' => 'col-form-label']) }}
    {!! Form::text('expires_at', null, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"minDate":"today", "enableTime":false, "dateFormat": "Y-m-d"}']) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('permission', __('Permission'), ['class' => 'col-form-label']) }}
    {!! Form::select('permission', $permissions, $share->permission,['class' => 'form-control globalOfSelect2']) !!}
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
