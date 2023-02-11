@if ($designation->id)
    {!! Form::model($designation, ['route' => ['admin.partner.designations.update', $designation->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($designation, ['route' => ['admin.partner.designations.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    <div class="form-group">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    <div class="form-group">
      {{ Form::label('company', __('Organization'), ['class' => 'col-form-label']) }}
      {!! Form::select('company', $companies, @$designation->company->id ?? null, [
        'class' => 'form-select globalOfSelect2',
        'data-updateOptions' => 'ajax-options',
        'data-href' => route('admin.partner.departments.getByCompany'),
        'data-target' => '#desgination-departments']) !!}
    </div>

    <div class="form-group">
      {{ Form::label('department', __('Department'), ['class' => 'col-form-label']) }}
      {!! Form::select('department', $departments, $designation->department_id, ['class' => 'form-select globalOfSelect2', 'id' => 'desgination-departments']) !!}
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
