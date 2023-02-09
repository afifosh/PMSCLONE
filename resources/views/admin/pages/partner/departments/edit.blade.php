@if ($department->id)
    {!! Form::model($department, ['route' => ['admin.partner.departments.update', $department->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($department, ['route' => ['admin.partner.departments.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    <div class="form-group">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    <div class="form-group">
      {{ Form::label('company', __('Company'), ['class' => 'col-form-label']) }}
      {!! Form::select('company', $companies, $department->company_id, [
        'class' => 'form-select globalOfSelect2',
        ]) !!}
    </div>

    <div class="form-group">
      {{ Form::label('parent_department', __('Parent Department'), ['class' => 'col-form-label']) }}
      {!! Form::select('parent_department', $departments, $department->parent_id, ['class' => 'form-select globalOfSelect2']) !!}
    </div>

    <div class="form-group">
      {{ Form::label('head', __('Department Head'), ['class' => 'col-form-label']) }}
      {!! Form::select('head', $admins, $department->head_id, ['class' => 'form-select globalOfSelect2']) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
