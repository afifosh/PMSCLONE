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
@includeWhen(isset($program),'admin.pages.programs.program-nav', ['tab' => 'invoices'])
{{-- Include Default Header --}}
@includeWhen(!isset($contract) && !isset($company) && !isset($program), 'admin.pages.invoices.header')

  <div class="mt-3  col-12">
    <div class="card">
      @if(isset($company) && !isset($contract) && !isset($program))
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
      @if (!isset($contract) && !isset($company) && !isset($program))
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

      $(document).on('click', '.invoice-check-all', function(){
        if($(this).is(':checked')){
          $('.invoice-check').prop('checked', true).trigger('change');
        }else{
          $('.invoice-check').prop('checked', false).trigger('change');
        }
      })

      $(document).on('click change', '.invoice-check', function(){
        if($('.invoice-check:checked').length == $('.invoice-check').length){
          $('.invoice-check-all').prop('checked', true);
        }else{
          $('.invoice-check-all').prop('checked', false);
        }

        // if atleast one is checked, show create-inv-btn
        if($('.invoice-check:checked').length > 0){
          $('.select-invoices-btn').addClass('d-none');
          $('.delete-inv-btn').removeClass('d-none');
        }else{
          $('.delete-inv-btn').addClass('d-none');
          $('.select-invoices-btn').removeClass('d-none');
        }
      })

      function destroyBulkInvoices(){
        var invoices = [];
        $('.invoice-check:checked').each(function(){
          invoices.push($(this).val());
        });
        if(invoices.length == 0){
          return;
        }
        $.ajax({
          url: route('admin.invoices.destroy', {'invoice': 'bulk'}),
          type: "DELETE",
          data: {
            invoices: invoices,
          },
          success: function(res){
            $('.invoice-check-all').prop('checked', false).trigger('change');
            $('.invoice-check').prop('checked', false).trigger('change');
            toast_success(res.message)
            toggleCheckboxes();
          }
        });
      }

      function toggleCheckboxes(){
        $('#invoices-table').DataTable().column(0).visible(!$('#invoices-table').DataTable().column(0).visible());
        $('#invoices-table').DataTable().ajax.reload();
      }
    </script>
@endpush
