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
    var expandedRow = null; // Variable to track the currently expanded row

    function createChildTable(row, contractId) {

        // Check if child table already exists, if so, destroy it
        var existingChildTable = $('#child-table-' + contractId).DataTable();
    if (existingChildTable) {
        existingChildTable.destroy();
    }


        if (expandedRow) {
            // Collapse all other rows in the table except the selected row
            table.rows().every(function() {
                var tr = this.node();
                if (tr !== row.node()) {
                    var otherRow = table.row(tr);
                    otherRow.child.hide();
                    $(tr).removeClass('shown');
                    $(tr).css('background-color', ''); // Reset the background color
                }
            });
        }

        // var childTableId = 'child-table-' + contractId;
        // var childTable = row.child('<table id="' + childTableId + '" class="display table dataTable" style="" width="100%"></table>').show();
    // 1st Row - For the pills
    var pills = `
        <tr class="child-row-added">
            <td colspan="100%"> 
                <ul class="nav nav-pills mt-3 mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-stages-${contractId}" aria-controls="child-stages-${contractId}" aria-selected="false">Stages</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#child-phases-${contractId}" aria-controls="child-phases-${contractId}" aria-selected="true">Phases</button>
                    </li>
                </ul>
            </td>
        </tr>
        
    `;

    row.child(pills).show();

    // 2nd Row - For the content
    var content = `
        <tr class="child-row-added p-0">
            <td class="p-0" colspan="100%">
                <div class="tab-content">
                    <div class="tab-pane fade" id="child-stages-${contractId}" role="tabpanel" aria-labelledby="child-stages-tab-${contractId}">
                        <!-- Stages content here -->
                    </div>
                    <div class="tab-pane fade show active" id="child-phases-${contractId}" role="tabpanel" aria-labelledby="child-phases-tab-${contractId}">
                        <!-- Phases content here -->
                        <!-- This is where your child table (Datatable) will go -->
                        <table id="child-table-${contractId}"></table>
                    </div>
                </div>
            </td>
        </tr>
    `;

    // Insert the content row after the pills row
   // $(row.node()).next().after(content);

    var contentRow = $(content);
contentRow.addClass("custom-content-row");
contentRow.css("background-color", "#f9f9f9"); // Example: change the background color
$(row.node()).next().after(contentRow);
    // Insert the content row after the pills row
  //  $(row.node()).after(content);
    var childTableId = "child-table-" + contractId;


    $('#' + childTableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('admin/contracts/paymentsplan/') }}/" + contractId + "/details",
                columns: [
                    { data: "stage_name", "name": "contract_stages.name", title: 'Stage Name' },
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
                dom: 'Blfrtip',
                buttons: [
        {
            text: 'Select Phases',
            className: 'btn btn-primary mx-3 select-phases-btn',
            action: function(e, dt, node, config) {
                toggleCheckboxes();
            }
        },
        {
            text: 'Create Invoices',
            className: 'btn btn-primary mx-3 create-inv-btn d-none',
            action: function(e, dt, node, config) {
                createInvoices();
            }
        },
        {
            text: 'Add Phase',
            className: 'btn btn-primary',
            action: function(e, dt, node, config) {
              //  var href = route('admin.projects.contracts.stages.phases.create', ['project' => 'project', $contract_id, $this->stage->id ?? 'stage']);
                // Assuming you have an AJAX modal function to open up the modal. If not, replace with your method.
             //   openAjaxModal('Add Phase', href);
            }
        }
    ],
                initComplete: function() {
                    // Move the buttons container near the search bar
                   // $('.dt-buttons', this.api().table().container()).appendTo($('.dataTables_filter', this.api().table().container()));
                }
            });


    }

    
    $('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
      var tr = $(this).closest('tr');
    var selectedRow = table.row(tr);
    var contractId = $(this).attr('contract-id');

    if (selectedRow.child.isShown()) {
        // Find any added child rows and remove them
        tr.nextAll('.child-row-added').remove();

        selectedRow.child.hide();
        $(tr).removeClass('shown');
        $(tr).css('background-color', '');
    } else {
        createChildTable(selectedRow, contractId);
        $(tr).addClass('shown');
        $(tr).css('background-color', '#f5f5f5');
    }
});

    $('.js-datatable-filter-form :input').on('change', function(e) {
        console.log('Filter changed');
        window.LaravelDataTables["paymentsplan-table"].draw();
    });

    $('#paymentsplan-table').on('preXhr.dt', function(e, settings, data) {
        console.log('PreXHR event');
        $('.js-datatable-filter-form :input').each(function() {
            data[$(this).prop('name')] = $(this).val();
        });
    });
});
</script>

@endpush
