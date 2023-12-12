/**************************
   * Phase create form js  **
   **************************/

  // calcDeductionAmount on change of downpayment_amount, dp_rate_id, is_manual_deduction, is_fixed_amount
  $(document).on('change keyup', '.phase-create-form [name="dp_rate_id"], .phase-create-form [name="is_manual_deduction"], .phase-create-form [name="is_fixed_amount"], .phase-create-form [name="downpayment_id"], .phase-create-form [name="calculation_source"], .phase-create-form [name="is_before_tax"]', function(){
    calcDeductionAmount();
  })

  function createPhasetax(phase_id)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $('#phase-addons tr:last').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.contracts.phases.taxes.create', { contract: window.active_contract ?? 0, phase: phase_id }),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $('#phase-addons tr:last').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function createPhaseDeduction(phase_id)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $('#phase-addons tr:last').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.contracts.phases.deductions.create', { contract: window.active_contract ?? 0, phase: phase_id }),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $('#phase-addons tr:last').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function editPhaseDetails(phase_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.projects.contracts.stages.phases.edit', { project:'project', contract: window.active_contract ?? 0, stage: 'stage', phase: phase_id, 'type': 'edit-form' }),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
        initFlatPickr()
      }
    });
  }

  function editPhaseTax(phase_id, pivot_tax_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.contracts.phases.taxes.edit', { contract: window.active_contract ?? 0, phase: phase_id, tax: pivot_tax_id }),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function editPhaseDeduction(phase_id, deduction_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.contracts.phases.deductions.edit', { contract: window.active_contract ?? 0, phase: phase_id, deduction: deduction_id }),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function editPhaseSubtotalAmount(phase_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.phases.subtotal-adjustments.create', { phase: phase_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
      }
    });
  }

  function editPhaseTotalAmount(phase_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.phases.total-amount-adjustments.create', { phase: phase_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
      }
    });
  }

  function reloadPhaseAddons(phase_id)
  {
    $.ajax({
      url: route('admin.contracts.phases.show', { contract: window.active_contract ?? 0, phase: phase_id, 'type': 'addons-list' }),
      type: "GET",
      success: function(res){
        $('#phase-addons').html(res.data.view_data);
        // init bs tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
      }
    });

    // if function reloadPhasesList exists, call it
    if(typeof reloadPhasesList == 'function'){
      reloadPhasesList();
    }
  }

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
    // expandPhase = JSON.parse(param).phase_id;
    const phase_id = JSON.parse(param).phase_id;
    // show loading in phase-addons
    $('#phase-addons').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    $.ajax({
      url: route('admin.contracts.phases.show', { contract: window.active_contract ?? 0, phase: phase_id, 'type': 'addons-list' }),
      type: "GET",
      success: function(res){
        $('#phase-addons').html(res.data.view_data);
        // init bs tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
      }
    });

    $('#phases-table').DataTable().ajax.reload(null, false);

    // if function reloadPhasesList exists, call it
    if(typeof reloadPhasesList == 'function'){
      reloadPhasesList();
    }
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
