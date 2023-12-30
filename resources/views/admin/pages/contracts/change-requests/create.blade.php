@if ($change_order->id)
    {!! Form::model($change_order, ['route' => ['admin.contracts.change-requests.update', ['contract' => $contract->id ?? 'contract']], 'method' => 'PUT']) !!}
@else
    {!! Form::model($change_order, ['route' => ['admin.contracts.change-requests.store',  ['contract' => $contract->id ?? 'contract']], 'method' => 'POST', 'id' => 'create-change-request-form']) !!}
@endif

<div id="crc-sec" class="row">
  <div class="d-flex">
    <div class="form-check me-3">
      <input class="form-check-input" type="radio" name="action_type" value="update-terms" checked id="update-terms-rad">
      <label class="form-check-label" for="update-terms-rad">
        Update Terms
      </label>
    </div>
    <div class="form-check me-3">
      <input class="form-check-input" type="radio" name="action_type" value="pause-contract" id="pause-contract">
      <label class="form-check-label" for="pause-contract">
        Pause
      </label>
    </div>
    <div class="form-check me-3">
      <input class="form-check-input" type="radio" name="action_type" value="resume-contract" id="resume-contract">
      <label class="form-check-label" for="resume-contract">
        Resume
      </label>
    </div>
    <div class="form-check me-3">
      <input class="form-check-input" type="radio" name="action_type" value="terminate-contract" id="terminate-contract">
      <label class="form-check-label" for="terminate-contract">
        Terminate
      </label>
    </div>
    <div class="form-check me-3">
      <input class="form-check-input" type="radio" name="action_type" value="early-completed-contract" id="early-completed-contract">
      <label class="form-check-label" for="early-completed-contract">
        Early Completed
      </label>
    </div>
  </div>
  {!! Form::hidden('change_request_type', null, ['id' => 'change-request-type', 'class' => 'dependent-select']) !!}
  @if(!$contract->id)
    <div class="mb-3">
      {!! Form::label('contract_id', __('Select Contract'), ['class' => 'col-form-label']) !!}
      {!! Form::select('contract_id', [], $contract->id ?? null, [
        'data-placeholder' => 'Select Contract',
        'class' => 'form-select globalOfSelect2Remote',
        'data-url' => route('resource-select', ['Contract', 'dependent_2_col' => 'change_request_type']),
        'data-dependent_2' => 'change-request-type',
        'id' => 'contract_id'
      ])!!}
    </div>
  @endif
  <div id="pause-contract-section" class="d-none ac-sec">
    {{-- <div class="card mt-2">
      <h5 class="card-header">Pause Contract</h5>
        <div class="card-body">
          <div class="row ms-3">
            <div class="row">
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="pause_until" value="manual" id="pause-manual" checked>
                <label class="form-check-label" for="pause-manual">
                  Pause Until I Resume
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="pause_until" value="custom_date" id="pause-custom">
                <label class="form-check-label" for="pause-custom">
                  Pause Until a specific date
                </label>
              </div>
              <div class="col-5 d-none pause-durantion">
                <div class="mb-3">
                  <input type="date" id="custom-date-value" name="custom_date_value" class="form-control flatpickr" data-flatpickr='{"minDate": "today"}' placeholder="Select Date">
                </div>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="pause_until" value="custom_date_from" id="pause-custom-from">
                <label class="form-check-label" for="pause-custom-from">
                  Pause from a date, until I resume
                </label>
              </div>
              <div class="col-5 d-none pause-durantion">
                <div class="mb-3">
                  <input type="date" id="custom-from-date-value" name="custom_from_date_value" class="form-control flatpickr" placeholder="Select Date">
                </div>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="pause_until" value="custom_unit" id="pause-days">
                <label class="form-check-label" for="pause-days">
                  Pause For
                </label>
              </div>
              <div class="col-5 d-none pause-durantion">
                <div class="mb-3 d-flex">
                  <span class="w-50">
                    <input type="number" id="unit-value" name="pause_for"class="form-control cusom_resum_parm">
                  </span>
                  <span class="w-50">
                    {!! Form::select('custom_unit', ['Days' => 'Days', 'Weeks' => 'Weeks', 'Months'=> 'Months'], null, ['class' => 'form-select select2 cusom_resum_parm']) !!}
                  </span>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="" class="form-label">Will Resume On: </label>
                    <input type="date" name="calculated_resumed_date" id="calculated_resumed_date" readonly class="form-control">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div> --}}
  </div>
  <div id="update-terms-sectoin" class="ac-sec">
    <div class="card mt-2">
      <h5 class="card-header p-3">Update Terms</h5>
      <div class="card-body row">
        <div class="form-group col">
          <label for="current_amount" class='col-form-label'>Current Value</label>
          {!! Form::text('current_amount', $contract->printable_value ?? null, ['class' => 'form-control', 'disabled','placeholder' => __('value'), 'data-value' => $contract->value ?? null]) !!}
        </div>
        <div class="form-group col-6 d-none n-value">
            <label for="new_value" class='col-form-label'>New Value</label>
            {!! Form::text('new_value', null, ['class' => 'form-control', 'disabled','placeholder' => __('Value')]) !!}
        </div>
        <div class="d-flex mt-2">
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="value_action" value="unchanged" id="unch-val-rd" checked>
            <label class="form-check-label" for="unch-val-rd">
              No Change
            </label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="value_action" value="inc" id="inc-val-rd">
            <label class="form-check-label" for="inc-val-rd">
              Increase Value
            </label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="value_action" value="dec" id="dec-val-rd">
            <label class="form-check-label" for="dec-val-rd">
              Decrease Value
            </label>
          </div>
        </div>
        <div class="form-group col-12 val-calc d-none">
          <label for="value" class='col-form-label'>Value Change</label>
          <div class="d-flex">
            <div>
              {!! Form::number('value_change', null, ['class' => 'form-control value_change', 'placeholder' => __('Value')]) !!}
            </div>
            {!! Form::select('currency', $currency ?? [], $contract->currency ?? null, [
              'data-placeholder' => 'Select Currency',
              'class' => 'form-select globalOfSelect2Remote value_change_currency',
              'data-url' => route('resource-select', ['Currency'])
              ])!!}
          </div>
        </div>
        <hr class="mt-2">
        <div class="form-group col">
          {{ Form::label('c_end_date', __('Current End Date'), ['class' => 'col-form-label']) }}
          {!! Form::date('c_end_date', $contract->end_date, ['class' => 'form-control', 'disabled','placeholder' => __('End Date')]) !!}
        </div>
        {{-- due date --}}
        <div class="form-group col-6 new-end-date d-none">
          {{ Form::label('new_end_date', __('New End Date'), ['class' => 'col-form-label']) }}
          {!! Form::date('new_end_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('End Date')]) !!}
        </div>
        <div class="d-flex mt-2">
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="timeline_action" value="unchanged" id="unch-timeline-rd" checked>
            <label class="form-check-label" for="unch-timeline-rd">
              No Change
            </label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="timeline_action" value="inc" id="inc-timeline-rd">
            <label class="form-check-label" for="inc-timeline-rd">
              Extend
            </label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="timeline_action" value="dec" id="dec-timeline-rd">
            <label class="form-check-label" for="dec-timeline-rd">
              Shorten
            </label>
          </div>
        </div>
        <div class="form-group col-12 timeline-calc d-none">
          <label for="value" class='col-form-label'>Value Change</label>
          <div class="d-flex">
            {!! Form::number('timeline_change', null, ['class' => 'form-control timeline_change', 'placeholder' => __('Value')]) !!}
            {!! Form::select('timeline_unit', ['Day' => 'Day(s)', 'Month' => 'Month(s)', 'Year' => 'Year(s)'], null, [
              'data-start-date' => $contract->start_date,
              'class' => 'form-select globalOfSelect2 timeline_change_unit'
              ])!!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="contract-terminate-section" class="d-none ac-sec">
    {{-- <div class="card mt-2">
      <h5 class="card-header p-3">Terminate Contract</h5>
      <div class="card-body">
        <div class="row ms-3">
          <div class="row">
            <div class="form-check mb-4">
              <input class="form-check-input" type="radio" name="terminate_date" value="now" id="terminate-now" checked>
              <label class="form-check-label" for="terminate-now">
                Terminate Immediately
              </label>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="terminate_date" value="custom" id="terminate-date">
              <label class="form-check-label" for="terminate-date">
                Terminate on a specific date
              </label>
            </div>
            <div class="mb-3 col-6 d-none">
              <input type="date" name="custom_date" id="custom-termination-date" class="form-control flatpickr" placeholder="Termination Date" data-flatpickr='{"minDate" : "today"}'>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
  </div>
  <div id="contract-resume-section" class="d-none ac-sec">
    {{-- <div class="card mt-2">
      <h5 class="card-header p-3">Resume Contract</h5>
      <div class="card-body">
        <div class="row ms-3"> --}}
          <div class="row">
            {{-- contract start date --}}
            <div class="form-group col">
              {{ Form::label('start_date', __('New Start Date'), ['class' => 'col-form-label']) }}
              {!! Form::date('start_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Start Date')]) !!}
            </div>

            {{-- contract end date --}}
            <div class="form-group col">
              {{ Form::label('end_date', __('New End Date'), ['class' => 'col-form-label']) }}
              {!! Form::date('end_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('End Date')]) !!}
            </div>
            {{-- <div class="form-check mb-4">
              <input class="form-check-input" type="radio" name="resume_date" value="now" id="resume-now" checked>
              <label class="form-check-label" for="resume-now">
                Resume Immediately
              </label>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="resume_date" value="custom" id="resume-date">
              <label class="form-check-label" for="resume-date">
                Resume on a specific date
              </label>
            </div>
            <div class="mb-3 col-6 d-none">
              <input type="date" name="custom_resume_date" id="custom-resume-date" class="form-control flatpickr" placeholder="Resume Date">
            </div> --}}
          </div>
        {{-- </div>
      </div>
    </div> --}}
  </div>

  {{-- requested at --}}
  <div class="form-group col-12">
    <label for="requested_at" class='col-form-label'>Requested At</label>
    {!! Form::date('requested_at', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Requested At')]) !!}
  </div>

  <div class="form-group mt-3 col-12">
    <label for="reason" class='col-form-label'>Reason</label>
    {!! Form::text('reason', null, ['class' => 'form-control', 'placeholder' => __('Reason')]) !!}
  </div>
  <div class="col-md-12 mt-3">
    <div class="mb-3">
      {!! Form::label('description', 'Description', ['class' => 'col-form-label']) !!}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) !!}
    </div>
  </div>


</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
<script>
  $(document).ready(function(){
    $('input[name="value_action"]').on('change click', function(){
      if($(this).val() == 'unchanged'){
        $('.n-value').addClass('d-none');
        $('.val-calc').addClass('d-none');
      }else if($(this).val() == 'inc' || $(this).val() == 'dec'){
        $('.n-value').removeClass('d-none');
        $('.val-calc').removeClass('d-none');
        calValue();
      }else{
        $('.n-value').addClass('d-none');
        $('.val-calc').removeClass('d-none');
      }
    });
  });
  $(document).on('change keyup', '.value_change', function(){
    calValue();
  });
  $('.value_change_currency').on('change', function(){
    calValue();
  });
  function calValue(){
    let val = $('.value_change').val() || 0;
    let action = $('input[name="value_action"]:checked').val();
    let cval = $('input[name="current_amount"]').data('value');
    let nval = 0;
    if(action == 'inc'){
      nval = parseFloat(cval) + parseFloat(val);
    }else if(action == 'dec'){
      nval = parseFloat(cval) - parseFloat(val);
    }
    let currency = $('.value_change_currency').val();
    $('input[name="new_value"]').val(currency + ' ' + nval.toFixed(2));
  }

  // timeline
  $(document).ready(function(){
    $('input[name="timeline_action"]').on('change click', function(){
      if($(this).val() == 'unchanged'){
        $('.new-end-date').addClass('d-none');
        $('.timeline-calc').addClass('d-none');
        $('input[name="new_end_date"]').val(null);
      }else if($(this).val() == 'inc' || $(this).val() == 'dec'){
        $('.new-end-date').removeClass('d-none');
        $('.timeline-calc').removeClass('d-none');
        calTimeline();
      }else{
        $('.new-end-date').addClass('d-none');
        $('.timeline-calc').removeClass('d-none');
      }
    });
  });

  $(document).on('change keyup', '.timeline_change', function(){
    calTimeline();
  });

  $('.timeline_change_unit').on('change', function(){
    calTimeline();
  });

  function calTimeline(){
    let val = $('.timeline_change').val() || 0;
    let action = $('input[name="timeline_action"]:checked').val();
    let cval = $('input[name="c_end_date"]').val();
    let nval = 0;
    let unit = $('.timeline_change_unit').val().toLowerCase();
    let start_date = $('.timeline_change_unit').data('start-date');
    if(action == 'inc'){
      // add without moment.js
      if(unit == 'day'){
        nval = new Date(cval);
        nval.setDate(nval.getDate() + parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }else if(unit == 'month'){
        nval = new Date(cval);
        nval.setMonth(nval.getMonth() + parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }else if(unit == 'year'){
        nval = new Date(cval);
        nval.setFullYear(nval.getFullYear() + parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }
    }else if(action == 'dec'){
      // subtract without moment.js
      if(unit == 'day'){
        nval = new Date(cval);
        nval.setDate(nval.getDate() - parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }else if(unit == 'month'){
        nval = new Date(cval);
        nval.setMonth(nval.getMonth() - parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }else if(unit == 'year'){
        nval = new Date(cval);
        nval.setFullYear(nval.getFullYear() - parseInt(val));
        nval = nval.toISOString().slice(0,10);
      }
    }
    // if(nval < start_date){
    //   // show validation error
    //   $('.timeline_change').addClass('is-invalid');
    // }else{
      $('.timeline_change').removeClass('is-invalid');
      $('input[name="new_end_date"]').val(nval);
    // }
    initFlatPickr();
  }

  $(document).on('change', '#contract_id', function(){
    let contract_id = $(this).val();
    if(contract_id){
      $.ajax({
        url: route('admin.contracts.show', {contract: contract_id, getjson: true}),
        type: 'GET',
        dataType: 'json',
        success: function(data){
          $('#create-change-request-form').attr('action', route('admin.contracts.change-requests.store', {contract: contract_id}));
          $('input[name="current_amount"]').val(data.value);
          $('input[name="current_amount"]').data('value', data.value);
          var end_date = new Date(data.end_date);
          end_date = end_date.toISOString().split('T')[0];
          $('input[name="c_end_date"]').val(end_date);
          $('#unch-val-rd').prop('checked', true).trigger("click").change();
          $('#unch-timeline-rd').prop('checked', true).trigger("click").change();
        }
      });
    }
  })

  // show appropriate section on radio change
  $(document).on('change', 'input[type=radio][name="action_type"]', function(){
    let val = $(this).val();
    $('.ac-sec').addClass('d-none');
    if(val == 'pause-contract'){
      $('#pause-contract-section').removeClass('d-none');
    }else if(val == 'update-terms'){
      $('#update-terms-sectoin').removeClass('d-none');
    } else if(val == 'resume-contract'){
      $('#contract-resume-section').removeClass('d-none');
    } else if(val == 'terminate-contract'){
      $('#contract-terminate-section').removeClass('d-none');
    }
  });

  // pause Form js
  $(document).on('change', 'input[name="pause_until"]', function() {
      if ($(this).val() == 'custom_date') {
        $('#custom-date-value').parents('.pause-durantion').removeClass('d-none');
      } else {
        $('#custom-date-value').parents('.pause-durantion').addClass('d-none');
      }

      if ($(this).val() == 'custom_date_from') {
        $('#custom-from-date-value').parents('.pause-durantion').removeClass('d-none');
      } else {
        $('#custom-from-date-value').parents('.pause-durantion').addClass('d-none');
      }

      if ($(this).val() == 'custom_unit') {
        $('#unit-value').parents('.pause-durantion').removeClass('d-none');
      } else {
        $('#unit-value').parents('.pause-durantion').addClass('d-none');
      }
  });
  // end pause Form js

  // termination form js
  $(document).on('change', 'input[name="terminate_date"]', function() {
    if ($(this).val() == 'custom') {
      $('#custom-termination-date').parent().removeClass('d-none');
    } else {
      $('#custom-termination-date').parent().addClass('d-none');
    }
  });
  // end termination form js

  // resume form js
  $(document).on('change', 'input[name="resume_date"]', function() {
    if ($(this).val() == 'custom') {
      $('#custom-resume-date').parent().removeClass('d-none');
    } else {
      $('#custom-resume-date').parent().addClass('d-none');
    }
  });
  // end resume form js

  // Resume form js
  $(document).on('change', '.cusom_resum_parm', function(){
      var unit = $('select[name="custom_unit"]').val();
      var value = $('input[name="pause_for"]').val();
      var date = new Date();
      if(unit == 'Days'){
          date.setDate(date.getDate() + parseInt(value));
      }else if(unit == 'Weeks'){
          date.setDate(date.getDate() + (parseInt(value) * 7));
      }else if(unit == 'Months'){
          date.setMonth(date.getMonth() + parseInt(value));
      }
      $('#calculated_resumed_date').val(date.toISOString().slice(0,10));
  });
  $(document).on('change', '#resume_now', function(){
      if($(this).is(':checked')){
          $('.resume_now_submit').removeClass('disabled');
      }else{
          $('.resume_now_submit').addClass('disabled');
      }
  });
  // end Resume form js

  // on change of radion button, update hidden field
  $(document).on('change', 'input[type=radio][name="action_type"]', function(){
    let val = $(this).val();
    $('#change-request-type').val(val).trigger('change');
  });

</script>
