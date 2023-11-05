@if ($contract->id)
    {!! Form::model($contract, ['route' => ['admin.contracts.update', $contract->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($contract, ['route' => ['admin.contracts.store'], 'method' => 'POST']) !!}
@endif
<div class="row">
  {{-- Subject --}}
  <div class="form-group col-6">
      {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
      {!! Form::text('subject', null, ['class' => 'form-control', 'placeholder' => __('Subject')]) !!}
  </div>
  {{-- project --}}
  <div class="form-group col-6">
    {{ Form::label('project_id', __('Project'), ['class' => 'col-form-label']) }}
    {!! Form::select('project_id', $projects ?? [], $contract->project_id, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['Project']),
      'data-placeholder' => __('Select Project'),
      'data-allow-clear' => 'true'
      ])!!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('currency', __('Currency'), ['class' => 'col-form-label']) }}
    {!! Form::select('currency', $currency ?? [], $contract->currency, [
      'data-placeholder' => 'Select Currency',
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['Currency']),
      'data-allow-clear' => 'true'
      ])!!}
  </div>
  {{-- value --}}
  <div class="form-group col-6">
    {{ Form::label('value', __('Contract Value'), ['class' => 'col-form-label']) }}
    {!! Form::number('value', $contract->value + $contract->total_tax_amount, ['class' => 'form-control', 'placeholder' => __('0.00')]) !!}
  </div>
  {{-- program --}}
  <div class="form-group col-6">
    {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
    {!! Form::select('program_id', $programs ?? [], $contract->program_id, [
    'class' => 'form-select globalOfSelect2Remote dependent-select',
    'data-url' => route('resource-select', ['Program']),
    'id' => 'contract-program-selected-id',
    'data-placeholder' => __('Select Program'),
    'data-allow-clear' => 'true'
    ]) !!}
  </div>
  {{-- Account --}}
  <div class="form-group col-6">
    {{ Form::label('account_balance_id', __('Account'), ['class' => 'col-form-label']) }}
    {!! Form::select('account_balance_id', $account_balanaces ?? [], $contract->account_balance_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['AccountBalance', 'dependent' => 'programId']),
    'data-dependent_id' => 'contract-program-selected-id',
    'data-placeholder' => __('Select Account'),
    'data-allow-clear' => 'true'
    ]) !!}
  </div>
  {{-- customer --}}
  <div class="form-group col-6">
    {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
    {!! Form::select('company_id', $companies ?? [], @$contract->assignable_type == 'App\Models\Company' && @$contract->assignable_id ? $contract->assignable_id : null, ['id' => 'contract-client-select',
    'class' => 'form-select globalOfSelect2UserRemote',
    'data-url' => route('resource-select-user', ['Company']),
    'data-placeholder' => __('Select Client'),
    'data-allow-clear' => 'true'
    ]) !!}
  </div>
  {{-- types --}}
  <div class="form-group col-6">
    {{ Form::label('type_id', __('Contract Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('type_id', $types, $contract->type_id, [
      'class' => 'form-select globalOfSelect2',
      'data-placeholder' => __('Contract Type'),
      'data-allow-clear' => 'true'
    ]) !!}
  </div>
  {{-- categories --}}
  <div class="form-group col-6">
    {{ Form::label('category_id', __('Contract Category'), ['class' => 'col-form-label']) }}
    {!! Form::select('category_id', $categories, $contract->category_id, [
      'class' => 'form-select globalOfSelect2',
      'data-placeholder' => __('Contract Category'),
      'data-allow-clear' => 'true'
    ]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('refrence_id', __('Refrence Id'), ['class' => 'col-form-label']) }}
    {!! Form::text('refrence_id', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Refrence Id')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('signature_date', __('Signature Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('signature_date', $contract->signature_date, ['class' => 'form-control flatpickr', 'required'=> 'true', 'placeholder' => __('Signature Date')]) !!}
  </div>
  {{-- invoicing method --}}
  <div class="form-group col-6">
    {{ Form::label('invoicing_method', __('Invoicing Method'), ['class' => 'col-form-label']) }}
    {!! Form::select('invoicing_method', ['Recuring' => 'Recuring', 'Phase Based' => 'Phase Based'], $contract->invoicing_method, ['class' => 'form-select globalOfSelect2']) !!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $contract->start_date, [
      'class' => 'form-control flatpickr',
      'placeholder' => __('Start Date'),
      'data-flatpickr' => '{"allowInput": true}'
    ]) !!}
  </div>
  {{-- end date --}}
  <div class="form-group col-6 end-date-sec {{!$contract->subject || $contract->end_date ? '' : 'd-none'}}">
    {{ Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('end_date', $contract->end_date, ['class' => 'form-control flatpickr', 'placeholder' => __('End Date')]) !!}
  </div>
  {{-- dute date --}}
  <div class="col-12 mt-2">
    <div class="d-flex">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="cont-has-end-date" @checked(!$contract->subject || $contract->end_date)>
        <label class="form-check-label me-2" for="cont-has-end-date">
          Has End Date
        </label>
      </div>
      <div class="form-check end-date-sec {{!$contract->subject || $contract->end_date ? '' : 'd-none'}}">
        <input class="form-check-input" type="checkbox" id="cal-cont-end-date">
        <label class="form-check-label" for="cal-cont-end-date">
          Calculate End Date
        </label>
      </div>
    </div>
  </div>
  <div class="d-none" id="end-date-cal-form">
    <hr>
    <div class="mb-3">
      <label for="" class="form-label">After From Start Date</label>
      <div class="d-flex">
        <div class="d-flex w-100">
          <input id="cont-add-count" type="number"  class="form-control cal-cont-end-date">
          {!! Form::select('null', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], null, ['class' => 'cont-add-unit cal-cont-end-date input-group-text form-select globalOfSelect2']) !!}
        </div>
      </div>
    </div>
    <hr>
  </div>
  <div class="form-group col-6">
    <label class="switch d-flex flex-column">
      {{ Form::label('visible_to_client', __('Visible to client'), ['class' => 'col-form-label']) }}
      {{ Form::checkbox('visible_to_client', 1, $contract->visible_to_client,['class' => 'switch-input is-invalid'])}}
      {{-- <input type="checkbox" class="switch-input is-invalid" checked /> --}}
      <span class="switch-toggle-slider position-relative mt-2">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label"></span>
    </label>
  </div>
  {{-- description --}}
  <div class="form-group col-12">
    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Description')]) !!}
  </div>
  {!! Form::hidden('isSavingDraft', null, ['id' => 'isSavingDraft']) !!}
</div>
<div class="mt-3 d-flex justify-content-between">
  <div class="contract-editing-users">
  </div>  
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      @if (!$contract->status || $contract->status == 'Draft')
        <button type="button" class="btn btn-dark" data-form="ajax-form" data-preAjaxAction="isSavingDraft" data-preAjaxParams="1">{{ __('Save Draft') }}</button>
      @endif
      <button type="submit" data-form="ajax-form" data-preAjaxAction="isSavingDraft" data-preAjaxParams="0" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>
{!! Form::close() !!}