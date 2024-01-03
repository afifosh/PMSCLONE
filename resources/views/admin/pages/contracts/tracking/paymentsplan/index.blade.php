@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Contracts Payments Plan Tracking Review')

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
<script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script src={{asset('assets/js/custom/admin-contract-phase-create.js')}}></script>
@endsection

@section('content')
  <div class="mt-3  col-12">
    {{-- Stats Start --}}
    <div class="card h-100">
      <div class="card-header">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">{{__('Contracts Payments Plan Tracking Review')}}</h5>
        </div>
      </div>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col">
            {!! Form::label('contracts', 'Contracts') !!}
            {!! Form::select('contracts', [], [], [
              'class' => 'form-select select2Remote',
              'id' => 'contracts-select', // Add the 'id' attribute here
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
            {!! Form::label('phase_reviewer', 'Reviewer') !!}
            {!! Form::select('phase_reviewer', [], 'all', [
                'class' => 'form-select select2UserRemote',
                'data-url' => route('resource-select-user', ['Admin', 'canReviewMutualContractsPhase']),
                'data-placeholder' => 'Select Reviewer',
                'data-allow-clear' => 'true'
            ]) !!}
          </div>
          <div class="col">
            {!! Form::label('phase_review_status', 'Review Status') !!}
            {!! Form::select('phase_review_status', $review_status, 'all', [
                'class' => 'form-select select2',
                'data-placeholder' => 'All Contracts',
                'data-placeholder' => 'Select Status',
                'data-allow-clear' => 'true',
            ]) !!}
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
    <div class="card-body main-table">
      {{$dataTable->table()}}
    </div>
    </div>
  </div>
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
<script>
  function reloadDataTables() {
    $('#paymentsplan-table').DataTable().ajax.reload(null, false);
  }

  $(document).ready(function() {
    var table = $('#paymentsplan-table').DataTable();
    var expandedRow = null; // Variable to track the currently expanded row

    $('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      var contractId = $(this).attr('contract-id'); // Assuming data-contract-id is stored on the button
      window.active_contract = contractId;
      // Set the value for the "Contracts" filter
      //$('#contracts').select2().val(contractId).trigger('change'); // Assuming your select field has an ID of "contracts" and uses Select2
      // Update the Select2 dropdown value
      // alert(contractId);
      // alert( $('#contracts-select').select2('data'));
      var $contractsSelect = $('#contracts-select'); // Assuming your select field has an ID of "contracts-select" and uses Select2
      // Check if the new value is different from the current value
      if ($contractsSelect.val() !== contractId) {
          // Append a new option and select it
          $contractsSelect.append(new Option('Contract Name', contractId, true, true));
          // Trigger the change event
          $contractsSelect.trigger('change');
      }else{
          console.log ($contractsSelect.val()  + "            " + contractId);
      }


    // $('#contracts-select').val(contractId).trigger('change');
    // $('#contracts-select').val(contractId).trigger('change');
      // Toggle expansion
      if (row.child.isShown()) {
        // The row is already open - close it and destroy all child tables
        destroyChildTables(contractId);
        row.child.hide();
        tr.removeClass('shown');
        // Clear the selection
        $contractsSelect.val(null).trigger('change');
                // Remove the 'child-row-added' rows which contain the pills and content
                tr.nextAll('tr.child-row-added').remove();
        expandedRow = null;
        window.selectedTab = null;
        window.active_contract = null;
      } else {
          // Open this row
          // Create new content for the child row
          var pillsRow = createPillsRow(contractId);
          var content = createContentRow(contractId);

          row.child(pillsRow).show();
          $(tr).addClass('shown');
          $(tr).css('background-color', '#f5f5f5');

          expandedRow = row;

          var contentRow = $(content);
          contentRow.addClass("custom-content-row");
          contentRow.css("background-color", "#f9f9f9"); // Example: change the background color
          $(row.node()).next().after(contentRow);
          $(row.node()).next().after(contentRow); // Append the content after the pills
      // Append the child tables to the expanded-details div

          // Initialize DataTables when tabs are clicked, not here
      }
      if(window.selectedTab) {
        $('[data-bs-target="' + window.selectedTab + '"]').click();
      }
});
    function destroyChildTables(contractId) {
    // Modify this function to destroy all the DataTables for the given contractId
    ['stages', 'phases', 'review'].forEach(function(section) {
        var tableId = `#${section}-table-${contractId}`;
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
    });
}

    // Event delegation for dynamic tabs


     $(document).on('click', '.nav-link[data-contract-id]', function() {
        var contractId = $(this).data('contract-id');
        var tabSelected = $(this).attr('data-bs-target');
        console.log('Tab clicked' + tabSelected + ' contractId ' + contractId ); // Add this line
        // Only initialize DataTable if it hasn't been initialized before
        window.selectedTab = tabSelected; // var to store the selected tab so we can select it after table reload
        switch (tabSelected) {
          case '#child-stages-' + contractId:
              loadStagesDataTable(contractId);
              break;
          case '#child-phases-' + contractId:
              loadPhasesDataTable(contractId);
              break;
          case '#child-review-' + contractId:
              loadReviewDataTable(contractId);
              break;
          default:
              console.error('No tab selected');
        }
    });

    function createPillsRow(contractId) {

        // Return the HTML for the navigation pills
        return `
            <tr class="child-row-added">
                <td colspan="100%">
                    <ul class="nav nav-pills mt-3 mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-stages-${contractId}" data-contract-id="${contractId}" aria-controls="child-stages-${contractId}" aria-selected="false">Stages</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-phases-${contractId}" data-contract-id="${contractId}" aria-controls="child-phases-${contractId}" aria-selected="true">Phases</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-review-${contractId}" data-contract-id="${contractId}" aria-controls="child-review-${contractId}" aria-selected="false">Review</button>
                        </li>
                    </ul>
                </td>
            </tr>
        `;
    }

    function createContentRow(contractId) {
        // Return the HTML for the tab content
        var content = `
            <tr class="child-row-added p-0">
                <td class="p-0" colspan="100%">
                    <div class="tab-content">
                        <div class="tab-pane fade" id="child-stages-${contractId}" role="tabpanel" aria-labelledby="child-stages-tab-${contractId}">
                            <table class="table" id="stages-table-${contractId}"></table>
                        </div>
                        <div class="tab-pane fade show active" id="child-phases-${contractId}" role="tabpanel" aria-labelledby="child-phases-tab-${contractId}">
                            <table class="table" id="phases-table-${contractId}"></table>
                        </div>
                        <div class="tab-pane fade" id="child-review-${contractId}" role="tabpanel" aria-labelledby="child-review-tab-${contractId}">
                            <table class="table" id="review-table-${contractId}"></table>
                        </div>
                    </div>
                </td>
            </tr>
        `;

        // var contentRow = $(content);
        // contentRow.addClass("custom-content-row");
        // contentRow.css("background-color", "#f9f9f9"); // Example: change the background color
        return content;
    }

    function loadStagesDataTable(contractId) {
      console.log('#stages-table-' + contractId);

        // Stages DataTable
        $('#stages-table-' + contractId).DataTable({
            processing: true,
            serverSide: true,
            ajax: route('admin.contracts.paymentsplan.stages', { contract: contractId }),
            columns: [
                // Define your stages columns here. I'm making some assumptions. Adjust accordingly.
                { data: "name", "name": "contract_stages.name", title: 'Stage Name' },
                { data: 'phases_count', title: 'Phases' },
                { data: 'start_date', title: 'Start Date' },
                { data: 'due_date', title: 'Due Date' },
                { data: 'total_amount', title: 'Amount' },
                { data: 'can_reviewed_by', title: 'Can Review'},
                { data: "reviewed_by", "reviewed_by": "reviewed_by", title: 'Reviewed By' },
                { data: 'my_review_progress', title: 'Progress'},
                { data: 'status', title: 'Status' },
                {
                            data: 'actions',
                            title: 'Actions',
                            orderable: false,
                            searchable: false
                        }

            ],
            buttons : [
                {
                    text: 'Add Stage',
                    className: 'btn btn-primary',
                    attr: {
                        'data-toggle': 'ajax-modal',
                        'data-title': 'Add Stage',
                        'data-href': route('admin.contracts.stages.create', { contract: contractId, tableId: ('stages-table-' + contractId)  })
                    }
                }
            ],
            destroy: true,
            dom: 'Blfrtip',
            initComplete: function() {
                    // Move the buttons container near the search bar
                   // $('.dt-buttons', this.api().table().container()).appendTo($('.dataTables_filter', this.api().table().container()));
                }    ,drawCallback: function(settings) {
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose'); // Dispose of any existing tooltips to prevent potential issues
        $('[data-bs-toggle="tooltip"]').tooltip(); // Re-initialize tooltips
    }
            // Add more DataTable options if required
        });
    }

    function loadPhasesDataTable(contractId) {
        $('#phases-table-' + contractId).DataTable({
                processing: true,
                serverSide: true,

                ajax: route('admin.contracts.paymentsplan.phases', { contract: contractId }),
                columns: [
                    { data: "checkbox", "name": "checkbox", title: '<input class="form-check-input phase-check-all" type="checkbox">', orderable: false, searchable: false},
                    { data: "stage_name", "name": "stage_name", title: 'Stage Name' },
                    { data: 'name', title: 'Phase Name' },
                    { data: 'start_date', title: 'Start Date' },
                    { data: 'due_date', title: 'Due Date' },
                    { data: 'amount', title: 'Amount' },
                    { data: 'can_reviewed_by', title: 'Can Review'},
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
          //    dom: '<"top"Bf>rt<"bottom"ip>',
           //  dom: 'ilpftr'
                buttons: [
        {
          text: 'Create Invoices',
          className: 'btn btn-primary mx-3 create-inv-btn disabled',
          attr: {
            'onclick': 'createInvoices()',
          }
        },
        {
            text: 'Add Phase',
            className: 'btn btn-primary',
            attr: {
                'data-toggle': 'ajax-modal',
                'data-title': 'Add Phase',
                'data-href': route('admin.projects.contracts.stages.phases.create', {project: 'project', contract: contractId, stage: 'stage', tableId: '#phases-table-' + contractId })
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

    function loadReviewDataTable(contractId) {
      console.log('#review-table-' + contractId);
            // Stages DataTable
            $('#review-table-' + contractId).DataTable({
                processing: true,
                serverSide: true,
                ajax: route('admin.contracts.paymentsplan.review', { contract: contractId }),
                columns: [
                    // Define your stages columns here. I'm making some assumptions. Adjust accordingly.
                    { data: "name", title: 'Reviewer Name' },
                    { data: 'review_status', title: 'Review Status' },


                ],
                destroy: true,
                // dom: 'Blfrtip',
                // Add more DataTable options if required
                initComplete: function() {
                    // Move the buttons container near the search bar
                   // $('.dt-buttons', this.api().table().container()).appendTo($('.dataTables_filter', this.api().table().container()));
                }    ,drawCallback: function(settings) {
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose'); // Dispose of any existing tooltips to prevent potential issues
        $('[data-bs-toggle="tooltip"]').tooltip(); // Re-initialize tooltips
    }
            });

    }

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
  var contractEditingUsers = [];
  var disableContractWhisper = false;
  window.oURL = window.location.href;
</script>
<script src="{{asset('assets/js/custom/contracts-realtime-updates.js')}}"></script>
{{-- End Real Time Editing Scripts --}}
@livewireScripts
<x-comments::scripts />
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
