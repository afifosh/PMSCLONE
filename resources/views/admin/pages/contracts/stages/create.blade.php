@if ($stage->id)
    {!! Form::model($stage, ['route' => ['admin.contracts.stages.update', ['project' => $project, 'contract' => $contract, 'stage' => $stage]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($stage, ['route' => ['admin.contracts.stages.store',  ['project' => $project, 'contract' => $contract]], 'method' => 'POST']) !!}
@endif
<div class="row">
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('estimated_cost', __('Estimated Cost'), ['class' => 'col-form-label']) }}
    <div class="dropdown open d-inline">
      <span data-bs-toggle="dropdown" aria-haspopup="true">
          <i class="fas fa-calculator"></i>
      </span>
      <div class="dropdown-menu p-3">
        <div class="mb-3" data-content="percent-cal">
          <label for="percent-value" class="form-label">Percentage (Balance: {{$contract->remaining_amount}})</label>
          <input type="number" name="percent-value" id="percent-value" data-balance="{{$contract->remaining_amount}}" class="form-control" placeholder="10%">
        </div>
      </div>
    </div>
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $stage->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'.$contract->start_date.'", "maxDate":"'.$contract->end_date.'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $stage->due_date, ['class' => 'form-control flatpickr', 'id' => 'stage_end_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'.$contract->start_date.'", "maxDate":"'.$contract->end_date.'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
  </div>
  <div class="col-12 mt-2">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="cal-stage-end-date">
      <label class="form-check-label" for="cal-stage-end-date">
        Calculate End Date
      </label>
    </div>
  </div>
  <div class="d-none" id="end-date-cal-form">
    <hr>
    <div class="mb-3">
      <label for="" class="form-label">After From Start Date</label>
      <div class="d-flex">
        <div class="d-flex w-100">
          <input id="stage-add-count" type="number"  class="form-control cal-stage-end-date">
          {!! Form::select('null', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], null, ['class' => 'stage-add-unit cal-stage-end-date input-group-text form-select globalOfSelect2']) !!}
        </div>
      </div>
    </div>
    <hr>
  </div>
  <div class="col-12 mt-2">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="is_committed" name="is_committed">
      <label class="form-check-label" for="is_committed">
        Is Committed
      </label>
    </div>
  </div>
  {{-- committed Amount --}}
  <div class="form-group col-6 d-none committed-amount">
    {{ Form::label('committed_amount', __('Committed Amount'), ['class' => 'col-form-label']) }}
    {!! Form::number('committed_amount', null, ['class' => 'form-control', 'placeholder' => __('Committed Amount')]) !!}
  </div>

</div>
<div class="col-md-12 mt-3">
  <div class="mb-3">
    {!! Form::label('description', 'Description', ['class' => 'col-form-label']) !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) !!}
  </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>

<script>
  $(document).on('change', '#cal-stage-end-date', function() {
    if ($(this).is(':checked')) {
      $('#end-date-cal-form').removeClass('d-none');
      calStageEndDate();
    } else {
      $('#end_date').val('');
      $('#end-date-cal-form').addClass('d-none');
      initFlatPickr();
    }
  });

  $(document).on('change', '#is_committed', function() {
    if ($(this).is(':checked')) {
      $('.committed-amount').removeClass('d-none');
    } else {
      $('.committed-amount').addClass('d-none');
    }
  });

  $(document).on('change', '[name="start_date"]', function(){
    calStageEndDate();
  })

  $(document).on('change keyup', '.cal-stage-end-date', function() {
    calStageEndDate();
  });

  function calStageEndDate()
  {
    const count = $('#stage-add-count').val();
    const unit = $('.stage-add-unit').val();
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
      $('#stage_end_date').val(endDate.toISOString().slice(0, 10));
    }else{
      $('#stage_end_date').val('');
    }
    initFlatPickr();
  }
  $('#percent-value').on('change keyup', function(){
    const percent = $(this).val();
    const balance = $(this).data('balance');
    if(percent && balance){
      const estimatedCost = (balance * percent) / 100;
      $('[name="estimated_cost"]').val(estimatedCost);
    }else{
      $('[name="estimated_cost"]').val('');
    }
  })
</script>
