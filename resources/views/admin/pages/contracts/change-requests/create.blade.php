@if ($change_order->id)
    {!! Form::model($change_order, ['route' => ['admin.contracts.change-requests.update', ['contract' => $contract]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($change_order, ['route' => ['admin.contracts.change-requests.store',  ['contract' => $contract]], 'method' => 'POST']) !!}
@endif

<div class="form-check">
<div class="row">
  <div class="form-group col">
    <label for="current_amount" class='col-form-label'>Current Value</label>
    {!! Form::text('current_amount', $contract->printable_value, ['class' => 'form-control', 'disabled','placeholder' => __('value'), 'data-value' => $contract->value]) !!}
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
      {!! Form::number('value_change', null, ['class' => 'form-control value_change', 'placeholder' => __('Value')]) !!}
      {!! Form::select('currency', $currency ?? [], $contract->currency, [
        'data-placeholder' => 'Select Currency',
        'class' => 'form-select globalOfSelect2Remote value_change_currency',
        'data-url' => route('resource-select', ['Currency'])
        ])!!}
    </div>
  </div>
  @if ($contract->end_date)
  <hr class="mt-2">
    <div class="form-group col">
      {{ Form::label('c_end_date', __('Current End Date'), ['class' => 'col-form-label']) }}
      {!! Form::date('c_end_date', $contract->end_date, ['class' => 'form-control', 'disabled','placeholder' => __('End Date')]) !!}
    </div>
    {{-- due date --}}
    <div class="form-group col-6 new-end-date d-none">
      {{ Form::label('new_end_date', __('New End Date'), ['class' => 'col-form-label']) }}
      {!! Form::date('new_end_date', null, ['class' => 'form-control', 'placeholder' => __('End Date')]) !!}
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
  @endif

  <div class="form-group col-12">
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

  }
</script>
