@if ($contact->id)
    {!! Form::model($contact, ['route' => ['company.contacts.update', $contact->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($contact, ['route' => ['company.contacts.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

@php
  $modifications = [];
  if (!is_array($contact->modifications) && $contact->modifications->count()) {
    $modifications = transformModifiedData($contact->modifications[0]->modifications);
    $contact = $modifications + $contact->toArray();
  }
@endphp

@if (is_a($contact, 'App\Models\Modification'))
  @php
      $contact = transformModifiedData($contact->modifications);
  @endphp
  {!! Form::hidden('model_type', 'pending_creation') !!}
@endif

@php
  $options = $options ?? [];
@endphp

<div class="row">
  <div class="form-group col-6">
    {{ Form::label('type', __('Contact Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('type', ['Owner', 'Employee'], $contact['type'], $options + ['class' => 'form-select globalOfSelect2']) !!}
    @modificationAlert(@$modifications['type'])
  </div>
  <div class="form-group col-6">
      {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
      {!! Form::text('title', $contact['title'], $options + ['class' => 'form-control', 'placeholder' => __('Title')]) !!}
      @modificationAlert(@$modifications['title'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('first_name', $contact['first_name'], $options + ['class' => 'form-control', 'placeholder' => __('First Name')]) !!}
    @modificationAlert(@$modifications['first_name'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('last_name', $contact['last_name'], $options + ['class' => 'form-control', 'placeholder' => __('Last Name')]) !!}
    @modificationAlert(@$modifications['last_name'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('position', __('Position'), ['class' => 'col-form-label']) }}
    {!! Form::text('position', $contact['position'], $options + ['class' => 'form-control', 'placeholder' => __('Position')]) !!}
    @modificationAlert(@$modifications['position'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
    {!! Form::text('phone', $contact['phone'], $options + ['class' => 'form-control', 'placeholder' => __('Phone')]) !!}
    @modificationAlert(@$modifications['phone'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('mobile', __('Mobile'), ['class' => 'col-form-label']) }}
    {!! Form::text('mobile', $contact['mobile'], $options + ['class' => 'form-control', 'placeholder' => __('Mobile')]) !!}
    @modificationAlert(@$modifications['mobile'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('fax', __('Fax'), ['class' => 'col-form-label']) }}
    {!! Form::text('fax', $contact['fax'], $options + ['class' => 'form-control', 'placeholder' => __('Fax')]) !!}
    @modificationAlert(@$modifications['fax'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
    {!! Form::text('email', $contact['email'], $options + ['class' => 'form-control', 'placeholder' => __('Email')]) !!}
    @modificationAlert(@$modifications['email'])
  </div>
  <div class="form-group col-6">
    {{ Form::label('poa', __('POA Letter'), ['class' => 'col-form-label']) }}
    {!! Form::file('poa', $options + ['class' => 'form-control']) !!}
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
