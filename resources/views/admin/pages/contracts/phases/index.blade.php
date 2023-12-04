@extends('admin/layouts/layoutMaster')

@section('title', $page.' Phases')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<style>
  .expanded-row{
    background-color: rgb(202, 202, 209) !important;
  }
</style>
@livewireStyles
<x-comments::styles />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-phases.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.active_project = '{{$contract->project_id ?? "project"}}';
  window.active_contract = '{{$contract->id}}';
  window.active_stage = '{{$stage->id ?? "stage"}}';
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/rrule@2.7.2/dist/es5/rrule.min.js"></script> --}}

<script>
$(document).ready(function(){
// console.log(rrule.rrulestr('DTSTART:20120201T023000Z\nRRULE:FREQ=MONTHLY;COUNT=5'));

});
function initSortable() {
  var sortable = Sortable.create($('#phases-table tbody')[0], {
    handle: '.bi-drag',
    group: 'shared',
    animation: 150,
    dataIdAttr: 'data-id',
    onSort: function (/**Event*/evt) {
      $.ajax({
        url: route('admin.projects.contracts.sort-phases', { project: window.active_project, contract: window.active_contract }),
        type: "PUT",
        data: {
          phases: sortable.toArray(),
        },
        success: function(res){
        }
      });
    },

  });
}

function createInvoices(){
  var phases = [];
  $('.phase-check:checked').each(function(){
    phases.push($(this).val());
  });
  if(phases.length == 0){
    return;
  }
  $.ajax({
    url: route('admin.contracts.bulk-invoices.store', { contract: window.active_contract }),
    type: "POST",
    data: {
      phases: phases,
    },
    success: function(res){
      $('.phase-check-all').prop('checked', false).trigger('change');
      $('.phase-check').prop('checked', false).trigger('change');
      toggleCheckboxes();
    }
  });
}

$(document).on('click', '.phase-check-all', function(){
  if($(this).is(':checked')){
    $('.phase-check').prop('checked', true).trigger('change');
  }else{
    $('.phase-check').prop('checked', false).trigger('change');
  }
})

$(document).on('click change', '.phase-check', function(){
  if($('.phase-check:checked').length == $('.phase-check').length){
    $('.phase-check-all').prop('checked', true);
  }else{
    $('.phase-check-all').prop('checked', false);
  }

  // if atleast one is checked, show create-inv-btn
  if($('.phase-check:checked').length > 0){
    $('.select-phases-btn').addClass('d-none');
    $('.create-inv-btn').removeClass('d-none');
  }else{
    $('.create-inv-btn').addClass('d-none');
    $('.select-phases-btn').removeClass('d-none');
  }
})

function toggleCheckboxes(){
  $('#phases-table').DataTable().column(1).visible(!$('#phases-table').DataTable().column(1).visible());
  $('#phases-table').DataTable().ajax.reload();
}

  /**************************
   * Phase create form js  **
   **************************/

  // calcDeductionAmount on change of downpayment_amount, dp_rate_id, is_manual_deduction, is_fixed_amount
  $(document).on('change keyup', '.phase-create-form [name="dp_rate_id"], .phase-create-form [name="is_manual_deduction"], .phase-create-form [name="is_fixed_amount"], .phase-create-form [name="downpayment_id"], .phase-create-form [name="calculation_source"], .phase-create-form [name="is_before_tax"]', function(){
    calcDeductionAmount();
  })

  function calcDeductionAmount()
  {
    var deductionAmount = parseFloat(0);
    var itemCost = parseFloat($('[name="estimated_cost"]').val());
    // if($('.phase-create-form [name="is_before_tax"]').val() == 0){
    //   itemCost = itemCost + calcPhaseTaxes();
    // }

    const downpaymentId = $('.phase-create-form [name="downpayment_id"]').val();
    if(downpaymentId && !$('.phase-create-form [name="is_manual_deduction"]').is(':checked') && !$('.phase-create-form [name="is_fixed_amount"]').is(':checked')){
      var deductionRate = parseFloat($('.phase-create-form [name="dp_rate_id"] option:selected').data('amount'));
      // is Percentage
      const isPercentageRate = $('.phase-create-form [name="dp_rate_id"] option:selected').data('type') == 'Percent';
      if(deductionRate){
        if(!isPercentageRate){
          deductionAmount = deductionRate;
        }else{
          // is before tax or after tax
          if($('.phase-create-form [name="calculation_source"]').val() == 'Down Payment'){
              const selectedDPTotal = parseFloat($('.phase-create-form [name="downpayment_id"] option:selected').data('amount'));
              deductionAmount = (selectedDPTotal * deductionRate) / 100;
            }else{
              deductionAmount = (itemCost * deductionRate) / 100;
            }
        }
      }
    }

    if($('.phase-create-form [name="is_manual_deduction"]').is(':checked') || $('.phase-create-form [name="is_fixed_amount"]').is(':checked')){
      deductionAmount = parseFloat($('.phase-create-form [name="downpayment_amount"]').val());
    }else{
      // set downpayment amount
      $('.phase-create-form [name="downpayment_amount"]').val(deductionAmount.toFixed(3));
    }

    return deductionAmount;
  }

  function calcPhaseTaxes(){
    var totalTax = parseFloat(0);
    let taxableAmount = getTaxableAmount();
    $('.phase-create-form .tax-rate').each(function (index, element) {
      // element == this
      $this = $(this);
      // if this element has removed-element class, skip it
      if($this.parents('[data-repeater-item]').hasClass('removed-element')){
        return;
      }
      const taxAmount = $this.find(':selected').data('amount');
      const taxType = $this.find(':selected').data('type');
      const taxCategory = $this.find(':selected').data('category');
      var amount = 0;
      if(taxType == 'Percent'){
        amount = (taxableAmount * taxAmount) / 100;
      }else{
        amount = taxAmount;
      }
      // if manual tax is checked, use manual tax amount
      if($this.parents('[data-repeater-item]').find('.is-manual-tax').is(':checked')){
        amount = parseFloat($this.parents('[data-repeater-item]').find('.tax-amount').val());
      }else if(amount != undefined){
        $(this).parents('[data-repeater-item]').find('.tax-amount').val(amount.toFixed(3));
      }
      // if tax category is 3 (reverse tax), skip it
      if(taxCategory == 3){
        return;
      }
      // if withholding tax use -ve amount
      if(taxCategory == 2){
        amount = -amount;
      }
      totalTax += amount;
    });

    return totalTax;
  }

  // on change is_manual_tax, enable/disable total_tax input
  $(document).on('change', '.phase-create-form [name="is_manual_tax"]', function(){
    $('.phase-create-form [name="total_tax"]').prop('disabled', !$(this).is(':checked'));
  })

  $(document).on('change keyup', '.phase-create-form [name="is_manual_tax"], .phase-create-form [name="tax"]', function(){
    calculateTax();
  })

  function calculateTax(){
    var taxableAmount = parseFloat($('.phase-create-form [name="estimated_cost"]').val());
    var totalTax = parseFloat(0);
    // if no tax selected || is_manual_tax checked, return
    if($('.phase-create-form [name="tax"]').val() == '' || $('.phase-create-form [name="is_manual_tax"]').is(':checked')){
      return;
    }
    var taxAmount = $('.phase-create-form [name="tax"] option:selected').data('amount');
    var taxType = $('.phase-create-form [name="tax"] option:selected').data('type');
    var taxCategory = $('.phase-create-form [name="tax"] option:selected').data('category');

    if(taxType == 'Percent'){
      totalTax = (taxableAmount * taxAmount) / 100;
    }else{
      totalTax = taxAmount;
    }
    // fill total_tax input
    $('.phase-create-form [name="total_tax"]').val(totalTax.toFixed(3));
  }
  var expandPhase = null;
  window.reloadTableAndActivePhase = function(param)
  {
    expandPhase = JSON.parse(param).phase_id;
    $('#phases-table').DataTable().ajax.reload();
  }

  window.expandPendingPhase = function()
  {
    if(expandPhase)
    $('#phase-ex-'+expandPhase).click();
  }

  // on change add_deduction show/hide deduction-inputs, if checked show else hide
  $(document).on('change', '.phase-create-form [name="add_deduction"]', function(){
    const isDeduction = $(this).is(':checked');
    if(isDeduction){
      $('.deduction-inputs').removeClass('d-none');
    }else{
      $('.deduction-inputs').addClass('d-none');
    }
  });

  // toggle manual deduction
  $(document).on('change', '.phase-create-form [name="is_manual_deduction"]', function(){
    $('.phase-create-form [name="downpayment_amount"]').prop('disabled', !$(this).is(':checked'));
  })

  // get downpayment info
  $(document).on('change', '.phase-create-form [name="downpayment_id"]', function(){
    const downpaymentId = $(this).val();
    if(!downpaymentId){
      return false;
    }
    $.ajax({
      url: route('admin.invoices.show', { invoice: downpaymentId, downpaymentjson: '1', itemId: '' }),
      type: 'GET',
      success: function (response) {
        var BsAlert = `
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div>
              <strong>Total Down Payment Amount:</strong> <span class="total_amount">${response.total_amount}</span>
              <br>
              <strong>Deducted Amount:</strong> <span class="deducted_amount">${response.total_deducted_amount}</span>
              <br>
              <strong>Remaining Amount:</strong> <span class="remaining_amount">${response.total_amount - response.total_deducted_amount}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `;
        $('.phase-create-form .downpayment-info').html(BsAlert);
      }
    })
  })

  // on change is_fixed_amount, show/hide and cal-deduction-section
  $(document).on('change', '.phase-create-form [name="is_fixed_amount"]', function(){
    if($(this).is(':checked')){
      $('.phase-create-form .cal-deduction-section').addClass('d-none');
      // enable downpayment_amount
      $('.phase-create-form [name="downpayment_amount"]').prop('disabled', false);
    }else{
      $('.phase-create-form .cal-deduction-section').removeClass('d-none');
      // disable downpayment_amount
      $('.phase-create-form [name="downpayment_amount"]').prop('disabled', true);
    }
  })
  /**
   * End Phase create form js
   */
</script>
@endsection

@section('content')
{{-- @includeWhen($page == 'Project', 'admin.pages.projects.navbar', ['tab' => 'phases']) --}}
@includeWhen($page == 'Contract', 'admin.pages.contracts.header', ['tab' => 'phases'])
@includeWhen($page == 'Contract All', 'admin.pages.contracts.header', ['tab' => 'all-phases'])
<div class="card mt-3">
  <div class="card-body">
    {{$dataTable->table()}}
  </div>
</div>
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      window.oURL = window.location.href;
    </script>
    <script>
      $(document).ready(function () {
        var table = $('#phases-table').DataTable();

        var expandedPhase = null;
        window.expandPhaseDetails = function(contract_id, phase_id, element)
        {
          if(expandedPhase){
            expandedPhase.child.hide();
            expandedPhase.row.child.hide();
            expandedPhase.child.remove();
            expandedPhase.row.child.remove();
            expandedPhase.child = null;
            expandedPhase.row.child = null;
            expandedPhase = null;
            // remove css color from expanded row
            $('#phases-table tbody tr').removeClass('expanded-row');
          }
          // show loading in child row
          $(element).closest('tr').after('<tr class="loading-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
          //
          // if will get data from ajax and add another row in the table. also remove older row if added with ajax
          $.ajax({
            type: "get",
            url: route('admin.contracts.phases.show', {contract: contract_id, phase: phase_id, 'type': 'expandDT' }),
            success: function (response) {
              var row = table.row(element.closest('tr'));
              var data = response.data;
              var child = row.child(data.view_data).show();
              expandedPhase = {
                row: row,
                child: child,
              };
              child.show();
              row.child(data.view_data).show();
              // add css color to expanded row
              $(element).closest('tr').addClass('expanded-row');
            }
          })
          .always(function(){
            // remove loading row
            $('.loading-row').remove();
          });

        }
      });
    </script>
    @livewireScripts
    <x-comments::scripts />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

