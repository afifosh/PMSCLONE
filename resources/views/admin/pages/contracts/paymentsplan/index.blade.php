@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Contracts Payments Plan')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
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
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
{{-- @include('admin.pages.contracts.header', ['tab' => 'stages']) --}}
  <div class="mt-3  col-12">
    {{-- Stats Start --}}
    <div class="card h-100">
      <div class="card-header">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">{{__('Contracts Payments Plan')}}</h5>
        </div>
      </div>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col">
            {!! Form::label('contracts', 'Contracts') !!}
            {!! Form::select('contracts', [], [], [
              'class' => 'form-select select2Remote',
              'data-placeholder' => __('All'),
              'data-allow-clear' => 'true',
              'data-url' => route('resource-select', ['Contract', 'hasContract'])
            ]) !!}
          </div>          
          <div class="col">
            {!! Form::label('programs', 'Programs') !!}
            {!! Form::select('programs', [], [], [
              'class' => 'form-select select2Remote',
              'data-placeholder' => __('All'),
              'data-allow-clear' => 'true',
              'data-url' => route('resource-select', ['Program', 'hasContract'])
            ]) !!}
          </div>
          <div class="col">
            {!! Form::label('project Status', 'Contract Status') !!}
            {!! Form::select('filter_status', $contract_statuses, null, ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
          </div>
          <div class="col">
            {!! Form::label('contract_type', 'Contract Type') !!}
            {!! Form::select('contract_type', $contractTypes, null, ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
          </div>
          <div class="col">
            {!! Form::label('companies', 'Client') !!}
            {!! Form::select('companies', [], null, [
              'class' => 'form-select select2Remote',
              'data-placeholder' => 'All Clients',
              'data-allow-clear' => 'true',
              'data-url' => route('resource-select', ['groupedCompany', 'hasContract']),
              ]) !!}
          </div>
        </div>
      </form>
    
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
 
 
 $(document).ready(function() {
    var table = $('#paymentsplan-table').DataTable();

    function createChildTable(row, contractId) {
        var childTableId = 'child-table-' + contractId;
        var childTable = row.child('<table id="' + childTableId + '" class="display" width="100%"></table>').show();
        
        $.get("{{ url('admin/contracts/paymentsplan/') }}/" + contractId + "/details", function(response) {

          var buttonsConfig = response.buttons.map(function(button) {
        return {
            text: button.text,
            className: button.className,
            action: function(e, dt, node, config) {
                // Check if the 'onclick' attribute exists and run the function
                if(button.attr && button.attr.onclick) {
                    eval(button.attr.onclick.replace('()', ''));
                }
            },
            attr: button.attr // Add attributes from the attr key in the JSON
        };
    });

            $('#' + childTableId).DataTable({
              data: response.data,
            columns: [
              { data: 'stage_name', title: 'Stage Name' },
              { data: 'name', title: 'Phase Name' },
              { data: 'start_date', title: 'Start Date' },
              { data: 'due_date', title: 'Due Date' },
              { data: 'amount', title: 'Amount' },
              { data: 'status', title: 'Status' },
              {
                  data: 'actions',
                  title: 'Actions',
                  orderable: false,
                  searchable: false
              }
            ],
            destroy: true,
            buttons: buttonsConfig 
            });
        });
    }

    $('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var contractId = $(this).attr('contract-id');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            tr.css('background-color', '');  // Reset the background color
        } else {
            createChildTable(row, contractId);
            tr.addClass('shown');
            tr.css('background-color', '#f5f5f5');  // Set a light gray background color
        }
    });

    $('.js-datatable-filter-form :input').on('change', function (e) {
        console.log('Filter changed');
        window.LaravelDataTables["paymentsplan-table"].draw();
    });

    $('#paymentsplan-table').on('preXhr.dt', function (e, settings, data) {
        console.log('PreXHR event');
        $('.js-datatable-filter-form :input').each(function () {
            data[$(this).prop('name')] = $(this).val();
        });
    });
});

</script>
@endpush
