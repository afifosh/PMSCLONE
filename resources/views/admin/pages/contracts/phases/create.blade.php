<div class="d-flex justify-content-start align-items-center user-name mb-4">
  <div class="avatar-wrapper">
    <div class="avatar me-2"><i class="ti ti-license mb-2 ti-xl"></i></div>
  </div>
  <div class="d-flex flex-column">
    <span class="fw-medium mb-1">{{ $contract->subject }}</span>
    <small class="text-muted mb-1">{{ $contract->program->name ?? 'N/A' }}</small>
    <span class="badge bg-label-{{$contract->getStatusColor()}} me-auto">{{$contract->status}}</span>
  </div>
</div>
<div class="row mb-4">
    <div class="nav-align-top">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-summary" aria-selected="true">Summary</button>
        </li>
        @if($phase->id)
          <li class="nav-item" onclick="reload_phase_activity({{$phase->id}})">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-activities" aria-selected="false">Activities</button>
          </li>
          <li class="nav-item" onclick="reload_phase_reviewers({{$phase->id}})">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-reviewers" aria-selected="false">Reviewers</button>
          </li>
          <li class="nav-item" onclick="reload_phase_comments({{$phase->id}})">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comments" aria-selected="false">Comments</button>
          </li>
        @endif
      </ul>
      <div class="tab-content p-0">
        <div class="tab-pane fade {{request()->tab == null || request()->tab == 'summary' ? 'show active' : ''}}" id="navs-top-summary" role="tabpanel">
          @include('admin.pages.contracts.phases.tab-summary')
        </div>
        <div class="tab-pane fade {{request()->tab == null || request()->tab == 'reviewers' ? 'show active' : ''}}" id="navs-top-reviewers" role="tabpanel">
          @include('admin.pages.contracts.phases.tab-reviewers')
        </div>
        <div class="tab-pane fade {{request()->tab == 'activities' ? 'show active' : ''}}" id="navs-top-activities" role="tabpanel">
        </div>
        <div class="tab-pane fade {{request()->tab == 'comments' ? 'show active' : ''}}" id="navs-top-comments" role="tabpanel">
        </div>
      </div>
    </div>
</div>


<script>
  function reload_phase_comments(phase){
    var url = route('admin.projects.contracts.stages.phases.edit', {
      project: 'project',
      contract: 'contract',
      stage: 'stage',
      phase: phase,
      tab: 'comments'
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-comments').html(data.data.view_data);
          Livewire.rescan(document.getElementById('navs-top-comments'));
          Alpine.initTree(document.getElementById('navs-top-comments'));
        setTimeout(function () {
          history.replaceState(null, null, oURL);
        }, 1000);
      }
    });
  }

  $(document).ready(function () {
        window.oURL = window.location.href;
      });

  function reload_phase_activity(phase){
    var url = route('admin.projects.contracts.stages.phases.edit', {
      project: 'project',
      contract: 'contract',
      stage: 'stage',
      phase: phase,
      tab: 'activity'
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-activities').html(data.data.view_data);
      }
    });
  }

  function reload_phase_reviewers(phase){
    var url = route('admin.projects.contracts.stages.phases.edit', {
      project: 'project',
      contract: 'contract',
      stage: 'stage',
      phase: phase,
      tab: 'reviewers'
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-activities').html(data.data.view_data);
      }
    });
  }

  $(document).on('change', '#cal-phase-end-date', function() {
    if ($(this).is(':checked')) {
      $('#end-date-cal-form').removeClass('d-none');
      calContEndDate();
    } else {
      $('#end_date').val('');
      $('#end-date-cal-form').addClass('d-none');
      initFlatPickr();
    }
  });

  $(document).on('change', '[name="start_date"]', function(){
    calContEndDate();
  })

  $(document).on('change keyup', '.cal-phase-end-date', function() {
    calContEndDate();
  });

  $(document).on('change', '[name="add-phase"]', function() {
    if ($(this).val() == 'rule') {
      $('.rr-single').addClass('d-none');
      $('.rr-rule').removeClass('d-none');
    } else {
      $('.rr-single').removeClass('d-none');
      $('.rr-rule').addClass('d-none');
    }
  }
  );

  function calContEndDate()
  {
    const count = $('#cont-add-count').val();
    const unit = $('.cont-add-unit').val();
    const startDate = $('#start_date').val();
    if(!startDate) return;
    if (count && unit) {
      let endDate = new Date(startDate);
      if(unit == 'Days') {
        endDate.setDate(endDate.getDate() + parseInt(count));
      } else if(unit == 'Weeks') {
        endDate.setDate(endDate.getDate() + (parseInt(count) * 7));
      } else if(unit == 'Months') {
        endDate.setMonth(endDate.getMonth() + parseInt(count));
      } else if(unit == 'Years') {
        endDate.setFullYear(endDate.getFullYear() + parseInt(count));
      }
      $('#phase_end_date').val(endDate.toISOString().slice(0, 10));
    }else{
      $('#phase_end_date').val('');
    }
    initFlatPickr();
  }

  $('#percent-value').on('change keyup', function(){
    const percent = $(this).val();
    const balance = $(this).data('balance');
    if(percent && balance){
      const estimatedCost = (balance * percent) / 100;
      $('[name="estimated_cost"]').val(estimatedCost).trigger('change');
    }else{
      $('[name="estimated_cost"]').val('').trigger('change');
    }
  })

  $(document).on('change click', '[name="phase_taxes[]"]', function(){
    calculateTotalCost();
  })
  $(document).on('change keyup', '[name="estimated_cost"]', function(){
    calculateTotalCost();
  })

  $(document).on('change keyup', '[name="manual_tax_amount"]', function(){
    calculateTotalCost();
  });

  $(document).on('change', '[name="is_manual_tax"]', function(){
    calculateTotalCost();
  });


  function calculateTotalCost()
  {
    const estimatedCost = $('[name="estimated_cost"]').val();

    var totalTax = parseFloat(0);
    const taxes = $('[name="phase_taxes[]"]').val();
    let totalCost = parseFloat(estimatedCost);
    if(estimatedCost && taxes){
      var percentagTax = 0;
      var fixedTax = 0;

      taxes.forEach(tax => {
        const taxAmount = $('[name="phase_taxes[]"] option[value="'+tax+'"]').data('amount');
        const taxType = $('[name="phase_taxes[]"] option[value="'+tax+'"]').data('type');
        if(taxType == 'Percent'){
          percentagTax += taxAmount;
        }else{
          fixedTax += taxAmount;
        }

      });

      totalTax += (totalCost * percentagTax) / 100;
      totalTax += fixedTax;

      totalCost += (totalCost * percentagTax) / 100;
      totalCost += fixedTax;

    }
    $('[name="total_tax"]').val(totalTax.toFixed(3));

    const manualTax = parseFloat($('[name="manual_tax_amount"]').val());
    if($('[name="is_manual_tax"]').is(':checked') && manualTax){
      totalCost = parseFloat(estimatedCost) + manualTax;
    }

    totalCost = totalCost.toFixed(3);
    $('[name="total_cost"]').val(totalCost);
    validateTotalCost();
  }

  $(document).on('change', '[name="total_cost"]', function(){
    validateTotalCost();
  })

  $(document).on('change', '[name="is_manual_tax"]', function (){
    if($(this).is(':checked')){
      $('[name="manual_tax_amount"]').parent().removeClass('d-none');
    }else{
      $('[name="manual_tax_amount"]').parent().addClass('d-none');
    }
  })

  function validateTotalCost(){
    let $this = $('[name="total_cost"]');
    $($this).parent().find('.validation-error').remove();
    const totalCost = $($this).val();
    const balance = $($this).data('max');
    if(totalCost && balance){
      // show validation error if total cost is greater than balance
      // if(totalCost > balance){
      //   $($this).after('<div class="text-danger validation-error">The total cost must not be greater than '+balance+'.</div>');
      // }
    }
  }
</script>
