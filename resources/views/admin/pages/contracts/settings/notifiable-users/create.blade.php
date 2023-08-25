{!! Form::model($model, ['route' => ['admin.contracts.notifiable-users.store', $contract], 'method' => 'POST']) !!}
<div class="row">
    @php
        $optionParameters = collect($admins)->mapWithKeys(function ($item) {
            return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
        })->all();
    @endphp
    <div class="form-group  col-12">
      {{ Form::label('users', __('Users'), ['class' => 'col-form-label']) }}
      {!! Form::select('users[]', $admins->pluck('email', 'id'), null, ['class' => 'form-select select2 globalOfSelect2User', 'multiple'], $optionParameters) !!}
    </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
