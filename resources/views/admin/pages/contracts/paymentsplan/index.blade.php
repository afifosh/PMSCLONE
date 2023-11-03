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
@livewireStyles
<x-comments::styles />
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
        function togglePhaseReviewStatus(buttonElement) {
            // Extract data attributes
            const contractId = buttonElement.getAttribute('data-contract-id');
            const phaseId = buttonElement.getAttribute('data-phase-id');
            const isReviewed = buttonElement.getAttribute('data-is-reviewed') === 'true';
    
            // Using the route function to dynamically generate the URL
            const url = route('admin.contracts.phases.toggle-review', {
                contract_id: contractId,
                phase_id: phaseId
            });
    
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Use the review status from the server response
                    const isReviewedFromResponse = data.data.isReviewed;

                    // Determine the button text and class based on the review status from the server response
                    const newText = isReviewedFromResponse ? 'MARK AS UNREVIEWED' : 'MARK AS REVIEWED';
                    const newClass = isReviewedFromResponse ? 'btn-label-danger' : 'btn-label-secondary';

                    // Update the button's text, data attribute, and class
                    buttonElement.textContent = newText;
                    buttonElement.setAttribute('data-is-reviewed', isReviewedFromResponse);
                    buttonElement.classList.remove('btn-label-secondary', 'btn-label-danger');
                    buttonElement.classList.add(newClass);
                    toast_success(data.message)
                } else {
                    alert('Error toggling review status.');
                    toast_danger(data.message)
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toast_danger('An unexpected error occurred.')
            });
        }
    </script>


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
                        <!-- Stages content here -->
                        <table class="table"  id="stages-table-${contractId}"></table>
                    </div>
                    <div class="tab-pane fade show active" id="child-phases-${contractId}" role="tabpanel" aria-labelledby="child-phases-tab-${contractId}">
                        <!-- Phases content here -->
                        <!-- This is where your child table (Datatable) will go -->
                        <table class="table" id="child-table-${contractId}"></table>
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

    // Stages DataTable
    $('#stages-table-' + contractId).DataTable({
        processing: true,
        serverSide: true,
        ajax: route('admin.contracts.paymentsplan.stages', { contract_id: contractId }),
        columns: [
            // Define your stages columns here. I'm making some assumptions. Adjust accordingly.
            { data: "name", "name": "contract_stages.name", title: 'Stage Name' },
            { data: 'phases_count', title: 'Phases' },
            { data: 'start_date', title: 'Start Date' },
            { data: 'due_date', title: 'Due Date' },
            { data: 'total_amount', title: 'Amount' },
            { data: 'status', title: 'Status' },
            {
                        data: 'actions',
                        title: 'Actions',
                        orderable: false,
                        searchable: false
                    }

        ],
        destroy: true
        // Add more DataTable options if required
    });

    $('#' + childTableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: route('admin.contracts.paymentsplan.phases', { contract_id: contractId }),
                columns: [
                    { data: "stage_name", "name": "stage_name", title: 'Stage Name' },
                    { data: 'name', title: 'Phase Name' },
                    { data: 'start_date', title: 'Start Date' },
                    { data: 'due_date', title: 'Due Date' },
                    { data: 'amount', title: 'Amount' },
                    { data: "reviewed_by", "reviewed_by": "reviewed_by", title: 'Reviewed By' },
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
                }    ,drawCallback: function(settings) {
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose'); // Dispose of any existing tooltips to prevent potential issues
        $('[data-bs-toggle="tooltip"]').tooltip(); // Re-initialize tooltips
    }
            });


    }


    var expandedRow = null; // Variable to track the currently expanded row

$('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
    var tr = $(this).closest('tr');
    var selectedRow = table.row(tr);
    var contractId = $(this).attr('contract-id');

    // If this row is already shown, hide it
    if (selectedRow.child.isShown()) {
        // Find any added child rows and remove them
        tr.nextAll('.child-row-added').remove();

        selectedRow.child.hide();
        $(tr).removeClass('shown');
        $(tr).css('background-color', '');
    } else {
        // If another row is expanded, collapse it
        if (expandedRow) {
            expandedRow.child.hide();
            $(expandedRow.node()).removeClass('shown');
            $(expandedRow.node()).css('background-color', '');
            $(expandedRow.node()).nextAll('.child-row-added').remove();
        }

        createChildTable(selectedRow, contractId);
        $(tr).addClass('shown');
        $(tr).css('background-color', '#f5f5f5');

        expandedRow = selectedRow; // Update the reference to the currently expanded row
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

        // Reload stages DataTable when "Stages" pill tab is clicked
        $(document).on('click', '.nav-link[data-bs-target^="#child-stages-"]', function() {
        // Extract the contractId from the target attribute of the clicked pill tab
        var contractId = $(this).attr('data-bs-target').replace("#child-stages-", "");

        // Get the corresponding stages DataTable and reload it
        $('#stages-table-' + contractId).DataTable().ajax.reload();
    });

    // Reload phases DataTable when "Phases" pill tab is clicked
    $(document).on('click', '.nav-link[data-bs-target^="#child-phases-"]', function() {
        // Extract the contractId from the target attribute of the clicked pill tab
        var contractId = $(this).attr('data-bs-target').replace("#child-phases-", "");

        // Get the corresponding phases DataTable and reload it
        $('#child-table-' + contractId).DataTable().ajax.reload();
    });
});
</script>

{{-- Real Time Editing Scripts --}}
<script>
  /***
   * Variables used in this file
   **/
  var activeContractId = "";
  var activeContractTab = "";
  var contractViewingUsers = [];
  var stageEditingUsers = [];
  var phaseEditingUsers = [];
  var disablePhaseWhisper = false;
  window.oURL = window.location.href;
</script>
<script src="{{asset('assets/js/custom/contracts-realtime-updates.js')}}"></script>
{{-- End Real Time Editing Scripts --}}
@livewireScripts
<x-comments::scripts />
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
