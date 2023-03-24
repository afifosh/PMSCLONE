@if ($contact->id)
    {!! Form::model($contact, ['route' => ['company.contacts.update', $contact->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($contact, ['route' => ['company.contacts.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif
@if ($contact::class == App\Models\Modification::class)
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
  </div>
  <div class="form-group col-6">
      {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
      {!! Form::text('title', $contact['title'], $options + ['class' => 'form-control', 'placeholder' => __('Title')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('first_name', $contact['first_name'], $options + ['class' => 'form-control', 'placeholder' => __('First Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('last_name', $contact['last_name'], $options + ['class' => 'form-control', 'placeholder' => __('Last Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('position', __('Position'), ['class' => 'col-form-label']) }}
    {!! Form::text('position', $contact['position'], $options + ['class' => 'form-control', 'placeholder' => __('Position')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
    {!! Form::text('phone', $contact['phone'], $options + ['class' => 'form-control', 'placeholder' => __('Phone')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('mobile', __('Mobile'), ['class' => 'col-form-label']) }}
    {!! Form::text('mobile', $contact['mobile'], $options + ['class' => 'form-control', 'placeholder' => __('Mobile')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('fax', __('Fax'), ['class' => 'col-form-label']) }}
    {!! Form::text('fax', $contact['fax'], $options + ['class' => 'form-control', 'placeholder' => __('Fax')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
    {!! Form::text('email', $contact['email'], $options + ['class' => 'form-control', 'placeholder' => __('Email')]) !!}
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
