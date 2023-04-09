@php
    $configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Kyc Documents')

@section('content')
  @if ($kyc_document->id)
    {!! Form::model($kyc_document, ['route' => ['admin.kyc-documents.update', $kyc_document], 'method' => 'PUT', 'class' => 'repeater']) !!}
  @else
    {!! Form::open(['route' => 'admin.kyc-documents.store', 'method' => 'POST', 'class' => 'repeater']) !!}
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
                            {!! Form::select('required_from', ['1' => 'Foreign/International', '2' => 'Local', '3' => 'Both'], null, ['class' => 'form-control select2']) !!}
                        </div>
                        <div class="form-group mb-2">
                            <label for="status" class="required">{{ __('Status:') }}</label>
                            {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control select2']) !!}
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
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][type]', array_combine($types, array_map('ucwords',$types)), $field['type'], ['class' => 'form-control']) !!}
                                  </div>
                                  <div>
                                    {!! Form::select('fields[][is_required]', ['Required', 'Not Required'], $field['is_required'], ['class' => 'form-control']) !!}
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
@endsection