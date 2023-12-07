
/************************************************************************************************************
 ********************************** Invoice Item Update Script **********************************************
 ***********************************************************************************************************/
function createItemtax(invoice_id, item_id, element)
  {
    console.log(invoice_id, item_id, element)
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('.table-responsive').find('tr:last').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.invoices.invoice-items.taxes.create', {invoice: invoice_id, invoice_item: item_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('.table-responsive').find('tr:last').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function createItemDeduction(invoice_id, item_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('.table-responsive').find('tr:last').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.invoices.invoice-items.deductions.create', {invoice: invoice_id, invoice_item: item_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('.table-responsive').find('tr:last').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function editItemDetails(invoice_id, item_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.invoices.invoice-items.edit', {invoice: invoice_id, invoice_item: item_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
        initFlatPickr()
      }
    });
  }

  function editItemTax(invoice_id, item_id,pivot_tax_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.invoices.invoice-items.taxes.edit', {invoice: invoice_id, invoice_item: item_id, tax: pivot_tax_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function editItemDeduction(invoice_id, item_id, deduction_id, element)
  {
    $('.expanded-edit-row').remove();
    // show loading in child row
    $(element).closest('tr').after('<tr class="loading-row expanded-edit-row my-5"><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: route('admin.invoices.invoice-items.deductions.edit', {invoice: invoice_id, invoice_item: item_id, deduction: deduction_id}),
      type: "GET",
      success: function(res){
        $('.expanded-edit-row').remove();
        $(element).closest('tr').after(res.data.view_data);
        initModalSelect2();
      }
    });
  }

  function reloadItemEditModal()
  {
    // check if invoice-item-edit-modal is in the dom
    if($('.invoice-item-edit-modal').length > 0){
      // is parent modal of that element is open
      if($('.invoice-item-edit-modal').closest('.modal').hasClass('show')){
        const invoiceId = $('.invoice-item-edit-modal').data('invoice-id');
        const itemId = $('.invoice-item-edit-modal').data('item-id');
        if(itemId && invoiceId){
           // add loadin in table of modal
          $('#item-edit-table-wrapper').html('<div class="text-center my-5"><div class="spinner-border text-primary mt-5" role="status"><span class="visually-hidden">Loading...</span></div></div>');
          $.ajax({
            url: route('admin.invoices.invoice-items.edit', {invoice: invoiceId, invoice_item: itemId, type: 'reload-modal'}),
            type: "GET",
            success: function(res){
              $('#item-edit-table-wrapper').html(res.data.view_data);
              initModalSelect2();
              initFlatPickr();
            }
          });
        }
      }
    }
  }
  /***********************************************************************************************************
  ********************************** End Invoice Item Update Script ******************************************
  ***********************************************************************************************************/


  /**********************************************************************************************************
   * ***************************************** Invoice Item Modal Script **************************************
   * ********************************************************************************************************/
  // toggle manual tax
  $(document).on('change', '#item-create [name="is_manual_tax"]', function(){
    $('#item-create [name="total_tax_amount"]').prop('disabled', !$(this).is(':checked'));
  })

  // any change in the form, calculate the total
  $(document).on('keyup change paste', '#item-create input, #item-create select, #item-create checkbox', function(){
    calculateCustomItemValues();
  })
  // toggle downpayment deduction
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    if($(this).val() && !$('#item-create [name="is_fixed_amount"]').is(':checked')){
      $('#item-create [name="dp_rate_id"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="dp_rate_id"]').parent().addClass('d-none');
    }
  })

  function calculateCustomItemValues (){
    let subtotal = getSubtotalAmount();

    if(!subtotal){
      return false;
    }

    //set subtotal
    $('#item-create [name="subtotal"]').val(subtotal.toFixed(3));

    // downpayment amount
    let totalDownpaymentAmount = calDPAmount();

    let totalTax = calItemTax();

    // if tax category is 2, then make total tax negative
    const taxCategory = $('#item-create [name="item_tax"]').find('option:selected').data('category');
    if(taxCategory == 2){
      totalTax = -totalTax;
    }else if(taxCategory == 3){
      totalTax = 0;
    }
    // total amount
    let totalAmount = subtotal + totalTax - totalDownpaymentAmount;

    // round total
    if($('#item-create [name="rounding_amount"]').is(':checked')){
      totalAmount = Math.trunc(totalAmount);
    }else{
      totalAmount = parseFloat(totalAmount).toFixed(3);
    }

    // set total amount
    $('#item-create [name="total"]').val(totalAmount);
  }

  function getSubtotalAmount(){
    const price = parseFloat($('[name="price"]').val());
    const quantity = parseFloat($('[name="quantity"]').val());
    let subtotal = price * quantity;

    // if price and quantity are not in the form, then get the subtotal from the form
    if(!subtotal){
      subtotal = parseFloat($('[name="subtotal"]').val());
    }

    return subtotal;
  }

  function calDPAmount()
  {
    let subtotal = parseFloat($('#item-create [name="subtotal"]').val());
    // if s_total is present in form
    if($('#item-create [name="t_total"]').length > 0 && $('#item-create [name="is_before_tax"]').val() == 0){
      subtotal = parseFloat($('#item-create [name="t_total"]').val());
    }
    let totalDownpaymentAmount = 0;

    const downpaymentId = $('#item-create [name="downpayment_id"]').val();
    if(downpaymentId && !$('#item-create [name="is_manual_deduction"]').is(':checked') && !$('#item-create [name="is_fixed_amount"]').is(':checked')){
      var deductionRate = parseFloat($('#item-create [name="dp_rate_id"] option:selected').data('amount'));
      // is Percentage
      const isPercentageRate = $('#item-create [name="dp_rate_id"] option:selected').data('type') == 'Percent';
      if(deductionRate){
        if(!isPercentageRate){
          totalDownpaymentAmount = deductionRate;
        }else{
          // is before tax or after tax
          if($('#item-create [name="is_before_tax"]').val() == 0){
            // source
            if($('#item-create [name="calculation_source"]').val() == 'Down Payment'){
              const selectedDPTotal = parseFloat($('#item-create [name="downpayment_id"] option:selected').data('amount'));
              totalDownpaymentAmount = (selectedDPTotal * deductionRate) / 100;
            }else{
              totalDownpaymentAmount = (subtotal * deductionRate) / 100;
            }
          }else{
            // source
            if($('#item-create [name="calculation_source"]').val() == 'Down Payment'){
              const selectedDPTotal = parseFloat($('#item-create [name="downpayment_id"] option:selected').data('amount'));
              totalDownpaymentAmount = (selectedDPTotal * deductionRate) / 100;
            }else{
              totalDownpaymentAmount = ((subtotal + calItemTax()) * deductionRate) / 100;
            }
          }
        }
      }
    }

    if($('#item-create [name="is_manual_deduction"]').is(':checked') || $('#item-create [name="is_fixed_amount"]').is(':checked')){
      totalDownpaymentAmount = parseFloat($('#item-create [name="downpayment_amount"]').val());
    }else{
      // set downpayment amount
      $('#item-create [name="downpayment_amount"]').val(totalDownpaymentAmount.toFixed(3));
    }

    return totalDownpaymentAmount;
  }

  function calItemTax()
  {
    let totalTax = 0;
    // calculate total tax
    var tax= $('#item-create [name="item_tax"]').val();
    if(tax && !$('#item-create [name="is_manual_tax"]').is(':checked')){
      let subtotal = parseFloat($('#item-create [name="subtotal"]').val());
      // is deduction before tax
      if($('#item-create [name="is_before_tax"]').val() == 1){
        subtotal -= calDPAmount();
      }

      if(tax){
        const taxAmount = parseFloat($('#item-create [name="item_tax"] option[value="'+tax+'"]').data('amount'));
        const taxType = $('#item-create [name="item_tax"] option[value="'+tax+'"]').data('type');
        if(taxType == 'Percent'){
          totalTax += (subtotal * taxAmount) / 100;
        }else{
          totalTax += taxAmount;
        }
      }
    }

    if($('#item-create [name="is_manual_tax"]').is(':checked')){
      totalTax = parseFloat($('#item-create [name="total_tax_amount"]').val());
    }else{
      // set total tax
      $('#item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));
    }

    return totalTax;
  }

  // toggle manual deduction
  $(document).on('change', '#item-create [name="is_manual_deduction"]', function(){
    $('#item-create [name="downpayment_amount"]').prop('disabled', !$(this).is(':checked'));
  })

  // get downpayment info
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    const downpaymentId = $(this).val();
    if(!downpaymentId){
      return false;
    }
    $.ajax({
      url: route('admin.invoices.show', { invoice: downpaymentId, downpaymentjson: '1', itemId: '$invoiceItem->id' }),
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
        $('#item-create .downpayment-info').html(BsAlert);
      }
    })
  })

  // on change is_fixed_amount, show/hide and cal-deduction-section
  $(document).on('change', '#item-create [name="is_fixed_amount"]', function(){
    if($(this).is(':checked')){
      $('#item-create .cal-deduction-section').addClass('d-none');
      // enable downpayment_amount
      $('#item-create [name="downpayment_amount"]').prop('disabled', false);
    }else{
      $('#item-create .cal-deduction-section').removeClass('d-none');
      // disable downpayment_amount
      $('#item-create [name="downpayment_amount"]').prop('disabled', true);
    }
  })

  /**********************************************************************************************************
  ****************************************** Invoice Item Modal Script **************************************
  **********************************************************************************************************/
