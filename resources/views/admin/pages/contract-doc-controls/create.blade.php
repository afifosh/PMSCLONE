@php
    $configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', $title)

@section('content')
  @if ($kyc_document->id)
    @if(request()->route()->getName() == 'admin.contract-doc-controls.edit')
      {!! Form::model($kyc_document, ['route' => ['admin.contract-doc-controls.update', $kyc_document], 'method' => 'PUT', 'class' => 'repeater']) !!}
    @else
      {!! Form::model($kyc_document, ['route' => ['admin.invoice-doc-controls.update', $kyc_document], 'method' => 'PUT', 'class' => 'repeater']) !!}
    @endif
  @else
    @if(request()->route()->getName() == 'admin.contract-doc-controls.create')
      {!! Form::open(['route' => 'admin.contract-doc-controls.store', 'method' => 'POST', 'class' => 'repeater']) !!}
    @else
      {!! Form::open(['route' => 'admin.invoice-doc-controls.store', 'method' => 'POST', 'class' => 'repeater']) !!}
    @endif
  @endif
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label for="title" class="required">{{ __('Title') }}</label>
                                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Enter Title']) !!}
                        </div>
                        <div class="form-group mb-2">
                          <label class="required">{{ __('Required From') }}</label>
                          {!! Form::select('client_type', ['Person' => 'Person', 'Company' => 'Company', 'Both' => 'Both'], null, ['class' => 'form-control select2']) !!}
                        </div>
                        {{-- types --}}
                        <div class="form-group">
                          {{ Form::label('contract_type_ids', __('Contract Type'), ['class' => 'col-form-label']) }}
                          {!! Form::select('contract_type_ids[]', $contract_types, $kyc_document->contractTypes->pluck('id')->toArray(), ['class' => 'form-select select2', 'multiple', 'data-placeholder' => __('Select Type')]) !!}
                        </div>
                        {{-- categories --}}
                        <div class="form-group">
                          {{ Form::label('contract_category_ids', __('Contract Category'), ['class' => 'col-form-label']) }}
                          {!! Form::select('contract_category_ids[]', $contract_categories, $kyc_document->contractCategories->pluck('id')->toArray(), ['class' => 'form-select select2', 'multiple', 'data-placeholder' => __('Select Category')]) !!}
                        </div>

                        @if(request()->route()->getName() == 'admin.invoice-doc-controls.create' || request()->route()->getName() == 'admin.invoice-doc-controls.edit' )
                         {{-- Contract --}}
                          <div class="form-group">
                            {{ Form::label('contract_ids', __('Contract'), ['class' => 'col-form-label']) }}
                            {!! Form::select('contract_ids[]', $contracts ?? [], $kyc_document->contracts->pluck('id')->toArray(), [
                              'class' => 'form-select select2Remote',
                              'multiple',
                              'data-placeholder' => __('Select Contract'),
                              'data-allow-clear' => 'true',
                              'data-url' => route('resource-select', ['Contract'])
                            ]) !!}
                          </div>
                          {{-- Invoice Type --}}
                          <div class="form-group">
                            {{ Form::label('invoice_type', __('Invoice type'), ['class' => 'col-form-label']) }}
                            {!! Form::select('invoice_type', (['' => 'Both'] + array_combine($invoice_types, $invoice_types)), $kyc_document->invoice_type , ['class' => 'form-select select2', 'data-placeholder' => __('Both'), 'data-allow-clear' => 'true']) !!}
                          </div>
                        @endif

                        <div class="form-group mb-2">
                            <label for="status" class="required">{{ __('Status:') }}</label>
                            {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control select2']) !!}
                        </div>
                        <div class="form-group mb-2">
                            <label for="is_mendatory" class="required">{{ __('Is Mendatory:') }}</label>
                            {!! Form::select('is_mendatory', ['1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control select2']) !!}
                        </div>
                        <div class="form-group mb-2">
                            <label for="description" class="required">{{ __('Description:') }}</label>
                            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) !!}
                        </div>
                        <div class="col-12 my-2">
                          <label class="switch">
                            {{ Form::checkbox('is_expirable', 1, $kyc_document->is_expirable,['class' => 'switch-input'])}}
                            <span class="switch-toggle-slider">
                              <span class="switch-on"></span>
                              <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">Is having Exipiry date?</span>
                          </label>
                        </div>
                        <div id="expirable_c" style="{{$kyc_document->is_expirable ? '' : 'display:none'}}">
                          <div class="form-group mb-2">
                              <label class="required">{{ __('Expiry Date Title:') }}</label>
                              {!! Form::text('expiry_date_title', null, ['class' => 'form-control', 'rows' => 2]) !!}
                          </div>

                          <div class="form-group mb-2">
                              <label for="is_expiry_date_required" class="required">{{ __('Is Expiry Date Required:') }}</label>
                              {!! Form::select('is_expiry_date_required', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control select2']) !!}
                          </div>
                        </div>

                        <button class="btn btn-primary basicbtn float-right" data-form="ajax-form">
                            <i class="fas fa-save"></i>
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Document Fields') }}</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-primary" data-repeater-create>
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body overflow-auto repeaters repeartor-height" data-repeater-list="fields">
                        @forelse ($kyc_document->fields ?? [] as $field)
                          <div class="form-group mb-4" data-repeater-item>
                              <div class="input-group d-flex flex-nowrap flex-row">
                                  <div class="form">
                                    {!! Form::text('fields[][label]', $field['label'], ['class' => 'form-control']) !!}
                                    {!! Form::hidden('fields[][id]', @$field['id'], ['class' => 'form-control']) !!}
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][type]', array_combine($types, array_map('ucwords',$types)), $field['type'], ['class' => 'form-control']) !!}
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][is_required]', ['Not Required', 'Required',], $field['is_required'], ['class' => 'form-control']) !!}
                                  </div>
                                  <button type="button" class="btn btn-danger" data-repeater-delete>
                                      <i class="fas fa-times-circle"></i>
                                  </button>
                              </div>
                          </div>
                        @empty
                          <div class="form-group mb-4" data-repeater-item>
                              <div class="input-group d-flex flex-nowrap flex-row">
                                  <div class="form">
                                    {!! Form::text('fields[][label]', null, ['class' => 'form-control']) !!}
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][type]', array_combine($types, array_map('ucwords',$types)), null, ['class' => 'form-control']) !!}
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][is_required]', ['Required', 'Not Required'], null, ['class' => 'form-control']) !!}
                                  </div>
                                  <button type="button" class="btn btn-danger" data-repeater-delete>
                                      <i class="fas fa-times-circle"></i>
                                  </button>
                              </div>
                          </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/scripts/repeater.js') }}"></script>
    <script src="{{ asset('assets/js/custom/select2.js') }}"></script>
    <script>
      $(document).on('click', '[name="is_expirable"]', function() {
        $('#expirable_c').toggle();
      });
    </script>
@endsection
