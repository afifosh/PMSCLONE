@if ($bank_account->id)
    {!! Form::model($bank_account, ['route' => ['company.bank-accounts.update', $bank_account->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($bank_account, ['route' => ['company.bank-accounts.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

@php
  $modifications = [];
  if (!is_array($bank_account->modifications) && $bank_account->modifications->count()) {
    $modifications = transformModifiedData($bank_account->modifications[0]->modifications);
    $ba_original = $bank_account;
    $bank_account = $modifications + $bank_account->toArray();
  }
@endphp

@if (is_a($bank_account, 'App\Models\Modification'))
  @php
      $bank_account_original = $bank_account;
      $bank_account = transformModifiedData($bank_account->modifications);
  @endphp
  {!! Form::hidden('model_type', 'pending_creation') !!}
@endif

@php
  $options = $options ?? [];
@endphp

@isset($bank_account_original)
    @forelse ($bank_account_original->disapprovals as $disapproval)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>{{$disapproval->reason}}</strong>
    </div>
    @empty
    @endforelse
@endisset

@if (@$ba_original->modifications[0]->disapprovals && $ba_original->modifications[0]->disapprovals->count())
    @forelse ($ba_original->modifications[0]->disapprovals as $disapproval)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>{{$disapproval->reason}}</strong>
    </div>
    @empty
    @endforelse
@endif

<div class="row">
  <div class="form-group col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
    {!! Form::select('country_id', $countries->prepend('Select Country', ''), $bank_account['country_id'], $options + ['class' => 'form-controll globalOfSelect2']) !!}
    @modificationAlert(@$modifications['country_id'])
  </div>
  <div class="form-group col-6">
      {{ Form::label('name', __('Bank Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', $bank_account['name'], $options + ['class' => 'form-control', 'placeholder' => __('Bank Name')]) !!}
      @modificationAlert(@$modifications['name'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('branch', __('Branch'), ['class' => 'col-form-label']) }}
    {!! Form::text('branch', $bank_account['branch'], $options + ['class' => 'form-control', 'placeholder' => __('Branch')]) !!}
    @modificationAlert(@$modifications['branch'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('street', __('Street'), ['class' => 'col-form-label']) }}
    {!! Form::text('street', $bank_account['street'], $options + ['class' => 'form-control', 'placeholder' => __('Street')]) !!}
    @modificationAlert(@$modifications['street'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
    {!! Form::text('city', $bank_account['city'], $options + ['class' => 'form-control', 'placeholder' => __('City')]) !!}
    @modificationAlert(@$modifications['city'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
    {!! Form::text('state', $bank_account['state'], $options + ['class' => 'form-control', 'placeholder' => __('State')]) !!}
    @modificationAlert(@$modifications['state'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('post_code', __('Postal Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('post_code', $bank_account['post_code'], $options + ['class' => 'form-control', 'placeholder' => __('Postal Code')]) !!}
    @modificationAlert(@$modifications['post_code'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('account_no', __('Account Number'), ['class' => 'col-form-label']) }}
    {!! Form::text('account_no', $bank_account['account_no'], $options + ['class' => 'form-control', 'placeholder' => __('Account Number')]) !!}
    @modificationAlert(@$modifications['account_no'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('iban_no', __('IBAN Number'), ['class' => 'col-form-label']) }}
    {!! Form::text('iban_no', $bank_account['iban_no'], $options + ['class' => 'form-control', 'placeholder' => __('IBAN Number')]) !!}
    @modificationAlert(@$modifications['iban_no'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('swift_code', __('Swift Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('swift_code', $bank_account['swift_code'], $options + ['class' => 'form-control', 'placeholder' => __('Swift Code')]) !!}
    @modificationAlert(@$modifications['swift_code'])
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
