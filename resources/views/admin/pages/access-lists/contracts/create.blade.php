@if ($aclRule->id)
  {!! Form::open(['route' => ['admin.admin-access-lists.contracts.update', ['admin_access_list' => $admin->id, 'contract' => $contract->id]],
      'method' => 'PUT',
      'id' => 'acl-create-form',
  ]) !!}
@else
  {!! Form::open(['route' => ['admin.admin-access-lists.contracts.store', ['admin_access_list' => $admin->id]],
  'method' => 'POST',
  'id' => 'acl-create-form',
  ]) !!}
@endif
<div class="row">
  <div class="form-group mb-3 col-sm-12">
      {{ Form::label('admin_id', __('User'), ['class' => 'col-form-label']) }}
      {!! Form::select('admin_id', [$admin->id => $admin->name], $admin->id, ['class' => 'form-select globalOfSelect2User', 'disabled']) !!}
  </div>
  <div class="form-group mb-3 col-sm-12">
      {{ Form::label('accessible_id', __('Contract'), ['class' => 'col-form-label']) }}
      {!! Form::select('accessible_id', isset($contract) ? [$contract->id => $contract->subject] : ['' => __('Select Contract')], $contract->id ?? null, ['class' => 'form-select globalOfSelect2Remote',
        $aclRule->id ? 'disabled' : '',
        'data-url' => route('resource-select', ['Contract', 'dnh_acl_rule_for' => $admin->id]),
        'data-allow-clear' => 'true',
        'data-placeholder' => __('Select Contract'),
      ]) !!}
  </div>
  <div class="col-6 mb-2 mt-4">
    <label class="switch">
      <input name="is_permanent_access" type="checkbox" class="switch-input" @checked(isset($aclRule->id) && !$aclRule->granted_till)>
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Permanent Access</span>
    </label>
  </div>
  <div class="form-group col-6 {{(isset($aclRule->id) && !$aclRule->granted_till) ? 'd-none' : ''}}">
      <label for="granted_till">Access Until:</label>
      {!! Form::text('granted_till', $aclRule->granted_till, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"minDate":"today","enableTime":false, "dateFormat": "Y-m-d",  "allowInput": false}',  'required'=> 'true']) !!}
  </div>
  {{-- select status --}}
  <div class="form-group col-12">
      {{ Form::label('is_revoked', __('Status'), ['class' => 'col-form-label']) }}
      {!! Form::select('is_revoked', ['1' => 'Revoked', '0' => 'Active'], $aclRule->is_revoked, ['class' => 'form-select globalOfSelect2']) !!}
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
