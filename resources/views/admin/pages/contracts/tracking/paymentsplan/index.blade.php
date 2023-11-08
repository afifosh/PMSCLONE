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
          <h5 class="card-title mb-0">{{__('Contracts Payments Plan Tracking Review')}}</h5>
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
                contract: contractId,
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
                    // alert('Error toggling review status.');
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
    var expandedRow = null;

    $('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var contractId = tr.data('contract-id');

        // Destroy any existing child tables to avoid memory leaks
        if ($.fn.DataTable.isDataTable('#child-table-' + contractId)) {
            $('#child-table-' + contractId).DataTable().destroy();
        }

        // If another row is expanded, hide it
        if (expandedRow && expandedRow.node() !== row.node()) {
            expandedRow.child.hide();
            $(expandedRow.node()).removeClass('shown');
        }

        // Expand or collapse this row
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            expandedRow = null;
        } else {
            // Insert the tabs (pills) and content rows for the selected row
            var pillsRow = createPillsRow(contractId);
            var contentRow = createContentRow(contractId);

            row.child(pillsRow + contentRow).show();
            tr.addClass('shown');
            expandedRow = row; // Keep track of the currently expanded row

            // Initialize any DataTables here, after the content is added to the DOM
            initializeChildDataTables(contractId);
        }
    });

    function createPillsRow(contractId) {
        // Return the HTML for the navigation pills
        return `
            <tr class="child-row-added">
                <td colspan="100%">
                    <ul class="nav nav-pills mt-3 mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-stages-${contractId}" aria-controls="child-stages-${contractId}" aria-selected="false">Stages</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#child-phases-${contractId}" aria-controls="child-phases-${contractId}" aria-selected="true">Phases</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-review-${contractId}" aria-controls="child-review-${contractId}" aria-selected="false">Review</button>
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
                            <table class="table" id="child-table-${contractId}"></table>
                        </div>
                        <div class="tab-pane fade" id="child-review-${contractId}" role="tabpanel" aria-labelledby="child-review-tab-${contractId}">
                            <table class="table" id="review-table-${contractId}"></table>
                        </div>                    
                    </div>
                </td>
            </tr>
        `;

        var contentRow = $(content);
        contentRow.addClass("custom-content-row");
        contentRow.css("background-color", "#f9f9f9"); // Example: change the background color
        return content;
    }

    function initializeChildDataTables(contractId) {
        // Initialize the DataTables for each tab's content
        // Example for one of the tables:
        // $('#child-table-' + contractId).DataTable({
        //     // DataTables configuration options
        // });
    }
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
