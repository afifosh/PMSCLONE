@if (isset($admin_id))
  {!! Form::open(['route' => ['admin.admin-access-lists.update', ['admin_access_list' => $admin_id]],
      'method' => 'PUT',
      'id' => 'acl-create-form',
  ]) !!}
@else
  {!! Form::model($adminAccessList, ['route' => ['admin.admin-access-lists.store'],
  'method' => 'POST',
  'id' => 'acl-create-form',
  ]) !!}
@endif
<div class="row">
  @php
    $optionParameters = collect($users)->mapWithKeys(function ($item) {
        return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
    })->all();
  @endphp

  <div class="form-group mb-3 col-sm-12">
      {{ Form::label('users[]', __('User'), ['class' => 'col-form-label']) }}
      {!! Form::select('users[]', $users->pluck('email', 'id'), $admin_id ?? null, ['class' => 'form-select globalOfSelect2User', isset($admin_id) ? 'disabled' : 'multiple', 'data-placeholder' => 'Select User', 'data-allow-clear' => 'true'], $optionParameters) !!}
  </div>
  <div class="col-6 mb-2 mt-4">
    <label class="switch">
      <input name="is_permanent_access" type="checkbox" class="switch-input" @checked(isset($admin_id) && !$granted_till)>
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Permanent Access</span>
    </label>
  </div>
  <div class="form-group col-6 {{(isset($admin_id) && !$granted_till) ? 'd-none' : ''}}">
      <label for="granted_till">Access Until:</label>
      {!! Form::text('granted_till',  $granted_till ?? null, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"minDate":"today","enableTime":false, "dateFormat": "Y-m-d",  "allowInput": false}',  'required'=> 'true']) !!}
  </div>
  <div class="position-relative mt-2">
    <label for="">Accessible Programms</label>
    <div class="col-12 acl-create-treeselect">
      {{--  --}}
    </div>
    {!! Form::text('accessible_programs', $category_id ?? null, ['class' => 'accessible-programs-input d-none']) !!}
  </div>
</div>
<div class="mt-3">
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>
{!! Form::close() !!}
<script>
  // if is_permanent_access is checked then hide the granted_till field, otherwise show it
  $(document).on('change', '#acl-create-form input[name="is_permanent_access"]', function() {
    if ($(this).is(':checked')) {
      $('input[name="granted_till"]').closest('.form-group').addClass('d-none');
    } else {
      $('input[name="granted_till"]').closest('.form-group').removeClass('d-none');
    }
  });
</script>
