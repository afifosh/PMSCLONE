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
      @if($contract->id)
        <li class="nav-item" onclick="reload_contract_activities({{$contract->id}})">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-activities" aria-selected="false">Activities</button>
        </li>
        <li class="nav-item" onclick="reload_contract_reviewers({{$contract->id}})">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-reviewers" aria-selected="false">Reviewers</button>
        </li>
        <li class="nav-item" onclick="reload_contract_comments({{$contract->id}})">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comments" aria-selected="false">Comments</button>
        </li>
      @endif
    </ul>
    <div class="tab-content p-0">
      <div class="tab-pane fade {{request()->tab == null || request()->tab == 'summary' ? 'show active' : ''}}" id="navs-top-summary" role="tabpanel">
        @include('admin.pages.contracts.tabs.summary')
      </div>
      <div class="tab-pane fade {{request()->tab == 'reviewers' ? 'show active' : ''}}" id="navs-top-reviewers" role="tabpanel">
        @include('admin.pages.contracts.tabs.reviewers')
      </div>
      <div class="tab-pane fade {{request()->tab == 'activities' ? 'show active' : ''}}" id="navs-top-activities" role="tabpanel">
        @include('admin.pages.contracts.tabs.activities')
      </div>
      <div class="tab-pane fade {{request()->tab == 'comments' ? 'show active' : ''}}" id="navs-top-comments" role="tabpanel">
        @include('admin.pages.contracts.tabs.comments')
      </div>
    </div>
  </div>
</div>

<script>

function toggleContractReviewStatus(buttonElement) {
            // Extract data attributes
            const contractId = buttonElement.getAttribute('data-contract-id');
            const isReviewed = buttonElement.getAttribute('data-is-reviewed') === 'true';

            // Using the route function to dynamically generate the URL
            const url = route('admin.contracts.toggle-review', {
                contract_id: contractId,
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
         
              
                  if (data.data.event == 'table_reload') {
                 
                  if (data.data.table_id != undefined && data.data.table_id != null && data.data.table_id != '') {
                    $('#' + data.data.table_id)
                      .DataTable()
                      .ajax.reload();
                  } 
             
                }
                if(data.data.close == 'globalModal'){
                  $('#globalModal').modal('hide');
       
          }else if(data.data.close == 'modal'){
            current.closest('.modal').modal('hide');
          }
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

  function isSavingDraft(isDraft){
    $('#isSavingDraft').val(isDraft);
  }
  $(document).on('change', '#contract-status', function() {
    if ($(this).val() == 'Terminated') {
      $('#termination_reason').parent().removeClass('d-none');
    } else {
      $('#termination_reason').parent().addClass('d-none');
    }
  });
  $(document).on('change', '#project-assign-to', function() {
    if ($(this).val() == 'Client') {
      $('#project-company-select').parent().addClass('d-none');
      $('#contract-client-select').parent().removeClass('d-none');
    } else {
      $('#project-company-select').parent().removeClass('d-none');
      $('#contract-client-select').parent().addClass('d-none');
    }
  });

  $(document).on('change', '#cal-cont-end-date', function() {
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

  $(document).on('change', '#cont-has-end-date', function() {
    if ($(this).is(':checked')) {
      $('.end-date-sec').removeClass('d-none');
      if($('#cal-cont-end-date').is(':checked')) {
        $('#end-date-cal-form').removeClass('d-none');
        calContEndDate();
      }
    } else {
      $('.end-date-sec').addClass('d-none');
      $('#end_date').val('');
      $('#end-date-cal-form').addClass('d-none');
    }
  });

  $(document).on('change keyup', '.cal-cont-end-date', function() {
    calContEndDate();
  });

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
      $('#end_date').val(endDate.toISOString().slice(0, 10));
    }else{
      $('#end_date').val('');
    }
    initFlatPickr();
  }
</script>

<script>


  $(document).ready(function () {
        window.oURL = window.location.href;
      });  
  function reload_contract_summary(contract){
    var url = route('admin.contracts.summary', {
      contract_id: contract,
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-summary').html(data.data.view_data);
      }
    });
  }

  function reload_contract_comments(contract){
    var url = route('admin.contracts.comments', {
      contract_id: contract,
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

  
  function reload_contract_activities(contract){
    var url = route('admin.contracts.activities', {
      contract_id: contract,
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-activities').html(data.data.view_data);
      }
    });
  }

  function reload_contract_reviewers(contract){
    var url = route('admin.contracts.reviewers', {
      contract_id: contract,
    });
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-reviewers').html(data.data.view_data);
      }
    });
  }

</script>
