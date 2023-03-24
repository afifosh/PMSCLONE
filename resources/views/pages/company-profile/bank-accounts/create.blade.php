@if ($bank_account->id)
    {!! Form::model($bank_account, ['route' => ['company.bank-accounts.update', $bank_account->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($bank_account, ['route' => ['company.bank-accounts.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

@if ($bank_account::class == App\Models\Modification::class)
  @php
      $bank_account = transformModifiedData($bank_account->modifications);
  @endphp
  {!! Form::hidden('model_type', 'pending_creation') !!}
@endif

@php
  $options = $options ?? [];
@endphp

<div class="row">
  <div class="form-group col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
    {!! Form::select('country_id', $countries->prepend('Select Country', ''), $bank_account['country_id'], $options + ['class' => 'form-controll globalOfSelect2']) !!}
  </div>
  <div class="form-group col-6">
      {{ Form::label('name', __('Bank Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', $bank_account['name'], $options + ['class' => 'form-control', 'placeholder' => __('Bank Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('branch', __('Branch'), ['class' => 'col-form-label']) }}
    {!! Form::text('branch', $bank_account['branch'], $options + ['class' => 'form-control', 'placeholder' => __('Branch')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('street', __('Street'), ['class' => 'col-form-label']) }}
    {!! Form::text('street', $bank_account['street'], $options + ['class' => 'form-control', 'placeholder' => __('Street')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
    {!! Form::text('city', $bank_account['city'], $options + ['class' => 'form-control', 'placeholder' => __('City')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
    {!! Form::text('state', $bank_account['state'], $options + ['class' => 'form-control', 'placeholder' => __('State')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('post_code', __('Postal Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('post_code', $bank_account['post_code'], $options + ['class' => 'form-control', 'placeholder' => __('Postal Code')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('account_no', __('Account Number'), ['class' => 'col-form-label']) }}
    {!! Form::text('account_no', $bank_account['account_no'], $options + ['class' => 'form-control', 'placeholder' => __('Account Number')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('iban_no', __('IBAN Number'), ['class' => 'col-form-label']) }}
    {!! Form::text('iban_no', $bank_account['iban_no'], $options + ['class' => 'form-control', 'placeholder' => __('IBAN Number')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('swift_code', __('Swift Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('swift_code', $bank_account['swift_code'], $options + ['class' => 'form-control', 'placeholder' => __('Swift Code')]) !!}
  </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        @empty($options)
            <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
        @endempty

    </div>
</div>
{!! Form::close() !!}
