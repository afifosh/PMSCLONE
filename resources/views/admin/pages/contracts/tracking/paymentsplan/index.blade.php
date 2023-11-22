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

        
function initTaxRepeater()
{
  $('.repeater').repeater({
      defaultValues: {
          'label': '',
          'type': 'text'
      },
      show: function () {
          $(this).slideDown();

          $(this).find('.select2').each(function() {
            if (!$(this).data('select2')) {
              var $this = $(this);
              $this.wrap('<div class="position-relative"></div>');
              $this.select2({
                dropdownParent: $this.parent()
              });
            }
          });
          calculateTotalCost();
          // $('.repeaters').animate({ scrollTop: 9999 }, 'slow');
      },
      hide: function (deleteElement) {
        // add class to delete element
        $(this).addClass('removed-element');
        $(this).slideUp(deleteElement);
        calculateTotalCost();
      },
      ready: function (setIndexes) {
          // $dragAndDrop.on('drop', setIndexes);
      },
      isFirstItemUndeletable: false
  })

  /**************************
   * Phase create form js  **
   **************************/
  $(document).on('change', '.is-manual-tax', function(){
    const isChecked = $(this).is(':checked');
    const parent = $(this).parents('[data-repeater-item]');
    const taxAmount = parent.find('.tax-amount');
    const taxRate = parent.find('.tax-rate');
    const taxRateAmount = taxRate.find(':selected').data('amount');
    const taxRateType = taxRate.find(':selected').data('type');
    if(isChecked){
      taxAmount.prop('disabled', false);
    }else{
      taxAmount.prop('disabled', true);
    }
    calculateTotalCost();
  })

  $(document).on('change keyup', '.pay-on-behalf, .is-authority-tax, .tax-amount, .tax-rate, [name="estimated_cost"], .is-manual-tax', function(){
    calculateTotalCost();
  })

  // if pay-on-behalf is checked, check and disable is-authority-tax
  $(document).on('change', '.pay-on-behalf', function(){
    const isChecked = $(this).is(':checked');
    const parent = $(this).parents('[data-repeater-item]');
    const isAuthorityTax = parent.find('.is-authority-tax');
    if(isChecked){
      isAuthorityTax.prop('checked', true);
      isAuthorityTax.prop('disabled', true);
    }else{
      isAuthorityTax.prop('disabled', false);
    }
  })

  function calculateTotalCost()
  {
    const estimatedCost = $('[name="estimated_cost"]').val();

    var totalTax = parseFloat(0);
    let totalCost = parseFloat(estimatedCost);
    if(!totalCost){
      return;
    }
    $('.phase-create-form .tax-rate').each(function (index, element) {
      // element == this
      $this = $(this);
      const taxAmount = $this.find(':selected').data('amount');
      const taxType = $this.find(':selected').data('type');
      var amount = 0;
      if(taxType == 'Percent'){
        amount = (estimatedCost * taxAmount) / 100;
      }else{
        amount = taxAmount;
      }
      // if manual tax is checked, use manual tax amount
      if($this.parents('[data-repeater-item]').find('.is-manual-tax').is(':checked')){
        amount = parseFloat($this.parents('[data-repeater-item]').find('.tax-amount').val());
      }else if(amount != undefined){
        $(this).parents('[data-repeater-item]').find('.tax-amount').val(amount.toFixed(3));
      }
      // if pay on behalf use -ve amount
      if($this.parents('[data-repeater-item]').find('.pay-on-behalf').is(':checked')){
        amount = -amount;
      }
      totalCost += amount;
    });
    totalCost = totalCost.toFixed(3);
    $('[name="total_cost"]').val(totalCost);
    validateTotalCost();
  }

  /**
   * End Phase create form js
   */
}        
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
    var expandedRow = null; // Variable to track the currently expanded row



    $('#paymentsplan-table tbody').on('click', '.btn-expand', function() {
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var contractId = $(this).attr('contract-id'); // Assuming data-contract-id is stored on the button
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

        // Only initialize DataTable if it hasn't been initialized before
        if (!$(tabSelected).hasClass('loaded')) {
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
            $(tabSelected).addClass('loaded');
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
