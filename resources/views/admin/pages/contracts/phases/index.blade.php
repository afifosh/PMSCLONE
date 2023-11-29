@extends('admin/layouts/layoutMaster')

@section('title', $page.' Phases')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
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

function initTaxRepeater()
{
  $('.repeater').repeater({
      defaultValues: {
          'label': '',
          'type': 'text'
      },
      show: function () {
          $(this).slideDown();
          // find selected options and remove selected from them
          $(this).find('option:selected').removeAttr('selected');
          $(this).find('.taxesSelect').each(function() {
            $(this).removeAttr('data-select2-id').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            // remove selected from options
            $(this).find('option:selected').removeAttr('selected');
            // remove data-select2-id from options
            $(this).find('option').removeAttr('data-select2-id');
            $(this).val(null).trigger('change');
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
              dropdownParent: $this.parent()
            });
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

  // if data-keep-item is 0, click data-repeater-delete of that element
  $('.repeater [data-keep-item="0"]').each(function(){
    $(this).find('[data-repeater-delete]').trigger('click');
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

  $(document).on('change keyup', '.tax-amount, .tax-rate, [name="estimated_cost"], .is-manual-tax, .phase-create-form :input, .phase-create-form select', function(){
    calculateTotalCost();
  })

  function calculateTotalCost()
  {
    console.log('calculateTotalCost')
    const estimatedCost = $('[name="estimated_cost"]').val();

    var totalTax = parseFloat(0);
    let totalCost = parseFloat(estimatedCost);
    if(!totalCost){
      return;
    }
    // taxes
    totalCost = totalCost + calcPhaseTaxes() - calcDeductionAmount();
    totalCost = totalCost.toFixed(3);
    $('[name="total_cost"]').val(totalCost);
    validateTotalCost();
  }

  function getTaxableAmount(){
    var estimated_cost = parseFloat($('[name="estimated_cost"]').val());
    // if !add_deduction, return estimated_cost
    if(!$('.phase-create-form [name="add_deduction"]').is(':checked')){
      return estimated_cost;
    }else if($('.phase-create-form [name="is_before_tax"]').val() == 1){
      console.log('is_before_taxTAbvl', $('.phase-create-form [name="is_before_tax"]').val());
      return estimated_cost - calcDeductionAmount();
    }else{
      return estimated_cost;
    }
  }

  function calcDeductionAmount()
  {
    var deductionAmount = parseFloat(0);
    var itemCost = parseFloat($('[name="estimated_cost"]').val());
    // if !add_deduction, return 0
    if(!$('.phase-create-form [name="add_deduction"]').is(':checked')){
      return deductionAmount;
    }

    if($('.phase-create-form [name="is_before_tax"]').val() == 0){
      console.log('is_before_tax', $('.phase-create-form [name="is_before_tax"]').val());
      itemCost = itemCost + calcPhaseTaxes();
    }

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
          if($('.phase-create-form [name="is_before_tax"]').val() == 0){
            // source
            if($('.phase-create-form [name="calculation_source"]').val() == 'Down Payment'){
              const selectedDPTotal = parseFloat($('.phase-create-form [name="downpayment_id"] option:selected').data('amount'));
              deductionAmount = (selectedDPTotal * deductionRate) / 100;
            }else{
              deductionAmount = (itemCost * deductionRate) / 100;
            }
          }else{
            // source
            if($('.phase-create-form [name="calculation_source"]').val() == 'Down Payment'){
              const selectedDPTotal = parseFloat($('.phase-create-form [name="downpayment_id"] option:selected').data('amount'));
              deductionAmount = (selectedDPTotal * deductionRate) / 100;
            }else{
              deductionAmount = ((itemCost + calcPhaseTaxes()) * deductionRate) / 100;
            }
          }
        }
      }
    }
console.log(deductionAmount);
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
    console.log('calcPhaseTaxes')
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

    console.log('totalTax:', totalTax);
    return totalTax;
  }

  // on change is_before_tax, if 1 move deduction section to before tax else move to after tax
  $(document).on('change', '.phase-create-form [name="is_before_tax"]', function(){
    const isBeforeTax = $(this).val();
    if(isBeforeTax == 1){
      $('.deduction-section').insertBefore('.taxes-section');
    }else{
      $('.deduction-section').insertAfter('.taxes-section');
    }
  });

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
}
</script>
@endsection

@section('content')
@includeWhen($page == 'Project', 'admin.pages.projects.navbar', ['tab' => 'phases'])
@includeWhen($page == 'Contract', 'admin.pages.contracts.header', ['tab' => 'phases'])
{{-- <div class="app-email mt-3 card">
  <div class="row g-0">
    <!-- Task Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Phase" data-href="{{}}">Add Phase</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Phase Status</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">All</span>
            </a>
            <div class="badge bg-label-success rounded-pill badge-center">{{$stage->phases->count()}}</div>
          </li>
          @forelse ($phase_statuses as $status)
            <li class="d-flex justify-content-between" data-target="{{slug($status)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="align-middle ms-2">{{$status}}</span>
              </a>
              <div class="badge bg-label-{{$colors[$status]}} rounded-pill badge-center">{{$stage->phases->where('status', $status)->count()}}</div>
            </li>
          @empty
          @endforelse
        </ul>
      </div>
    </div>
    <!--/ Task Sidebar -->

    <!-- Task List -->
    <div class="col app-emails-list">
      <div class="shadow-none border-0">
        <div class="emails-list-header p-3 py-lg-3 py-2">
          <!-- Task List: Search -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center w-100">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3"></i>
              <div class="mb-0 mb-lg-2 w-100">
                <div class="input-group input-group-merge shadow-none">
                  <span class="input-group-text border-0 ps-0" id="email-search">
                    <i class="ti ti-search"></i>
                  </span>
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search Phase">
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-0 mb-md-2">
              <i class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1" onclick="refreshPhaseList();"></i>
            </div>
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Task List: Items -->
        <div class="email-list pt-0">
          <ul class="list-unstyled m-0 todo-task-list tasks-list tasks">
            @include('admin.pages.contracts.phases.phase-list')
          </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Task List -->
  </div>
</div> --}}
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
    @livewireScripts
    <x-comments::scripts />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

