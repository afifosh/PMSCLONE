@if ($contract->id)
    {!! Form::model($contract, ['route' => ['admin.contracts.update', $contract->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($contract, ['route' => ['admin.contracts.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- Subject --}}
    <div class="form-group col-6">
        {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
        {!! Form::text('subject', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Subject')]) !!}
    </div>
    {{-- value --}}
    <div class="form-group col-6">
      {{ Form::label('value', __('Contract Value'), ['class' => 'col-form-label']) }}
      {!! Form::number('value', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Contract Value')]) !!}
    </div>
    {{-- project --}}
    <div class="form-group col-6">
      {{ Form::label('project_id', __('Project'), ['class' => 'col-form-label']) }}
      {!! Form::select('project_id', $projects, $contract->project_id, [
        'class' => 'form-select globalOfSelect2',
        'data-updateOptions' => 'ajax-options',
        'data-href' => route('admin.projects.getCompanyByProject'),
        'data-target' => '#project-company-select']) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('assign_to', __('Assign To'), ['class' => 'col-form-label']) }}
      {!! Form::select('assign_to', ['Company' => 'Company', 'Client' => 'Client'], @$contract->client_id ? 'Client' : 'Company', ['id' => 'project-assign-to', 'class' => 'form-select globalOfSelect2']) !!}
    </div>
    {{-- customer --}}
    <div class="form-group col-6 {{$contract->client_id ? 'd-none' : ''}}">
      {{ Form::label('company_id', __('Company'), ['class' => 'col-form-label']) }}
      {!! Form::select('company_id', $companies, @$contract->company_id ?? null, ['id' => 'project-company-select', 'class' => 'form-select globalOfSelect2']) !!}
    </div>
    <div class="form-group col-6 {{!$contract->client_id ? 'd-none' : ''}}">
      {{ Form::label('client_id', __('Client'), ['class' => 'col-form-label']) }}
      {!! Form::select('client_id', $clients, @$contract->Client_id ?? null, ['id' => 'contract-client-select', 'class' => 'form-select globalOfSelect2']) !!}
    </div>
    {{-- start date --}}
    <div class="form-group col-6">
      {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
      {!! Form::date('start_date', $contract->start_date, ['class' => 'form-control flatpickr', 'required'=> 'true', 'placeholder' => __('Start Date')]) !!}
    </div>
    {{-- dute date --}}
    <div class="form-group col-6">
      {{ Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) }}
      {!! Form::date('end_date', $contract->end_date, ['class' => 'form-control flatpickr', 'required'=> 'true', 'placeholder' => __('End Date')]) !!}
    </div>
    {{-- types --}}
    <div class="form-group col-6">
      {{ Form::label('type_id', __('Contract Type'), ['class' => 'col-form-label']) }}
      {!! Form::select('type_id', $types, $contract->type_id, ['class' => 'form-select globalOfSelect2']) !!}
    </div>
    @if ($contract->id)
      {{-- status --}}
      <div class="form-group col-6">
        {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
        {!! Form::select('status', array_combine($statuses, $statuses), $contract->status, ['id' => 'contract-status', 'class' => 'form-select globalOfSelect2']) !!}
      </div>
    @endif
    <div class="form-group col-12 {{$contract->status != 'Terminated' ? 'd-none' : ''}}">
      {{ Form::label('termination_reason', __('Termination Reason'), ['class' => 'col-form-label']) }}
      {!! Form::text('termination_reason', $termination_reason ?? null, ['id' => 'termination_reason', 'class' => 'form-control', 'placeholder' => __('Termination Reason')]) !!}
    </div>
    {{-- description --}}
    <div class="form-group col-12">
      {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Description')]) !!}
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
<script>
  $(document).on('change', '#contract-status', function() {
    if ($(this).val() == 'Terminated') {
      $('#termination_reason').parent().removeClass('d-none');
    } else {
      $('#termination_reason').parent().addClass('d-none');
    }
  });
  $(document).on('change', '#project-assign-to', function() {
    if ($(this).val() == 'Client') {
      $('#project-company-select').parent().addClass('d-none');
      $('#contract-client-select').parent().removeClass('d-none');
    } else {
      $('#project-company-select').parent().removeClass('d-none');
      $('#contract-client-select').parent().addClass('d-none');
    }
  });
</script>
