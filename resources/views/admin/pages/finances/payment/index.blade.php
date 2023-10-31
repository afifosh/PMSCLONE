@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Payments')

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

@includeWhen(isset($contract),'admin.pages.contracts.header', ['tab' => 'payments'])
@includeWhen(isset($company),'admin.pages.company.header', ['tab' => 'payments'])
@includeWhen(isset($program),'admin.pages.programs.program-nav', ['tab' => 'payments'])
  <div class="mt-3  col-12">
    <div class="card">
      @if (!isset($contract) && isset($company) && isset($program))
        <h5 class="card-header">Search Filter</h5>
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col-4">
              {!! Form::label('filter_contract', 'Contract') !!}
              {!! Form::select('filter_contract', [], '', [
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['Contract', 'haspayments', 'companies' => $company->id]),
                'data-placeholder' => __('All Contracts'),
                'data-allow-clear' => 'true',
              ]) !!}
            </div>
            <div class="col-4">
              {!! Form::label('filter_invoice_type', 'Invoice Type') !!}
              {!! Form::select('filter_invoice_type', $invoice_types, '', ['class' => 'form-select select2']) !!}
            </div>
          </div>
        </form>
      @endif
      @if (!isset($contract) && !isset($company) && isset($program))
        <h5 class="card-header">Search Filter</h5>
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col">
              {!! Form::label('filter_company', 'Client') !!}
              {!! Form::select('filter_company', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => __('All Clients'),
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['groupedCompany', 'haspayments']),
              ])!!}
            </div>
            <div class="col">
              {!! Form::label('filter_contract', 'Contract') !!}
              {!! Form::select('filter_contract', [], '', [
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['Contract', 'haspayments']),
                'data-placeholder' => __('All Contracts'),
                'data-allow-clear' => 'true',
              ]) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_invoice', 'Invoice') !!}
              {!! Form::select('filter_invoice', [], '', [
                'class' => 'form-select select2Remote',
                'data-placeholder' => __('All Invoices'),
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['Invoice', 'haspayments']),
                ]) !!}
            </div>
            <div class="col">
              {!! Form::label('filter_invoice_type', 'Invoice Type') !!}
              {!! Form::select('filter_invoice_type', $invoice_types, '', ['class' => 'form-select select2']) !!}
            </div>
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
              window.LaravelDataTables["payments-table"].draw();
          });

          $('#payments-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });

      $(document).on('click', '.payment-check-all', function(){
        if($(this).is(':checked')){
          $('.payment-check').prop('checked', true).trigger('change');
        }else{
          $('.payment-check').prop('checked', false).trigger('change');
        }
      })

      $(document).on('click change', '.payment-check', function(){
        if($('.payment-check:checked').length == $('.payment-check').length){
          $('.payment-check-all').prop('checked', true);
        }else{
          $('.payment-check-all').prop('checked', false);
        }

        // if atleast one is checked, show create-inv-btn
        if($('.payment-check:checked').length > 0){
          $('.select-payments-btn').addClass('d-none');
          $('.delete-inv-btn').removeClass('d-none');
        }else{
          $('.delete-inv-btn').addClass('d-none');
          $('.select-payments-btn').removeClass('d-none');
        }
      })

      function destroyBulkPayments(){
        var payments = [];
        $('.payment-check:checked').each(function(){
          payments.push($(this).val());
        });
        if(payments.length == 0){
          return;
        }
        $.ajax({
          url: route('admin.finances.payments.destroy', {'payment': 'bulk'}),
          type: "DELETE",
          data: {
            payments: payments,
          },
          success: function(res){
            $('.payment-check-all').prop('checked', false).trigger('change');
            $('.payment-check').prop('checked', false).trigger('change');
            toast_success(res.message)
            toggleCheckboxes();
          }
        });
      }

      function toggleCheckboxes(){
        $('#payments-table').DataTable().column(0).visible(!$('#payments-table').DataTable().column(0).visible());
        $('#payments-table').DataTable().ajax.reload();
      }
    </script>
@endpush
