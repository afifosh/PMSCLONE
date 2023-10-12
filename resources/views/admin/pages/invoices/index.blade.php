@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Invoices')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')

@includeWhen(isset($contract),'admin.pages.contracts.header', ['tab' => 'invoices'])
@includeWhen(isset($company),'admin.pages.company.header', ['tab' => 'invoices'])
{{-- Include Default Header --}}
@includeWhen(!isset($contract) && !isset($company), 'admin.pages.invoices.header')

  <div class="mt-3  col-12">
    <div class="card">
      @if(isset($company) && !isset($contract))
        <h5 class="card-header">Search Filter</h5>
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col-4">
              {!! Form::label('filter_contract', 'Contract') !!}
              {!! Form::select('filter_contract', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => 'All',
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['Contract', 'hasinv', 'companies' => $company->id])
              ]) !!}
            </div>
            <div class="col-4">
              {!! Form::label('filter_due_date', 'Due Date') !!}
              {!! Form::select('filter_due_date', [
                '' => 'All',
                'Overdue' => ['over_due' => 'Overdue'],
                'Month' => [
                  'this_month' => 'This Month',
                  'prev_month' => 'Previous Month',
                  'next_month' => 'Next Month',
                ],
                'Quarter' => [
                  'this_quarter' => 'This Quarter',
                  'prev_quarter' => 'Previous Quarter',
                  'next_quarter' => 'Next Quarter',
                ],
                ], '',
                [
                  'class' => 'form-select select2',
                  'data-placeholder' => 'All',
                  'data-allow-clear' => 'true'
                ]
              ) !!}
            </div>
          </div>
        </form>
      @endif
      @if (!isset($contract) && !isset($company))
        <h5 class="card-header">Search Filter</h5>
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col">
              {!! Form::label('filter_contract', 'Contract') !!}
              {!! Form::select('filter_contract', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => 'All',
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['Contract', 'hasinv'])
              ]) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_company', 'Client') !!}
              {!! Form::select('filter_company', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => 'All',
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['groupedCompany', 'hasinv'])
              ]) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_status', 'Status') !!}
              {!! Form::select('filter_status', $invoice_statuses, '', ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_type', 'Type') !!}
              {!! Form::select('filter_type', $invoice_types, '', ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_due_date', 'Due Date') !!}
              {!! Form::select('filter_due_date', [
                '' => 'All',
                'Overdue' => ['over_due' => 'Overdue'],
                'Month' => [
                  'this_month' => 'This Month',
                  'prev_month' => 'Previous Month',
                  'next_month' => 'Next Month',
                ],
                'Quarter' => [
                  'this_quarter' => 'This Quarter',
                  'prev_quarter' => 'Previous Quarter',
                  'next_quarter' => 'Next Quarter',
                ],
                ], '',
                [
                  'class' => 'form-select select2',
                  'data-placeholder' => 'All',
                  'data-allow-clear' => 'true'
                ]
              ) !!}
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">

          </div>
        </form>
      @endif
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["invoices-table"].draw();
          });

          $('#invoices-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
