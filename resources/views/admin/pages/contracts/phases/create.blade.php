@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.update', ['project' => $project, 'contract' => $contract, 'phase' => $phase->id, 'stage' => $stage]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.store',  ['project' => $project, 'contract' => $contract, 'stage' => $stage]], 'method' => 'POST']) !!}
@endif

{{-- <div class="form-check">
  <input class="form-check-input" type="radio" name="add-phase" value="single" id="add-phase" checked>
  <label class="form-check-label" for="add-phase">
    Single
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="add-phase" value="rule" id="add-phase-rule" >
  <label class="form-check-label" for="add-phase-rule">
    Rule
  </label>
</div> --}}
<div class="row rr-single">
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
          <label for="percent-value" class="form-label">Percentage (Balance: {{$remaining_amount}})</label>
          <input type="number" name="percent-value" id="percent-value" data-balance="{{$remaining_amount}}" class="form-control" placeholder="10%">
        </div>
      </div>
    </div>
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $phase->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. (isset($stage->start_date) ? $stage->start_date : $contract->start_date) .'", "maxDate":"'. (isset($stage->due_date) ? $stage->due_date : $contract->end_date ).'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $phase->due_date, ['class' => 'form-control flatpickr', 'id' => 'phase_end_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. (isset($stage->start_date) ? $stage->start_date : $contract->start_date) .'", "maxDate":"'. (isset($stage->due_date) ? $stage->due_date : $contract->end_date ).'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
  </div>
  <div class="col-12 mt-2">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="cal-phase-end-date">
      <label class="form-check-label" for="cal-phase-end-date">
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
          <input id="cont-add-count" type="number"  class="form-control cal-phase-end-date">
          {!! Form::select('null', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], null, ['class' => 'cont-add-unit cal-phase-end-date input-group-text form-select globalOfSelect2']) !!}
        </div>
      </div>
    </div>
    <hr>
  </div>

</div>
<div class="row rr-rule d-none">
  {{-- <div class="form-group col-6">
    {{ Form::label('start_date', __('Contract Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $contract->start_date, ['class' => 'form-control', 'disabled','placeholder' => __('Start Date')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Contract Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $contract->end_date, ['class' => 'form-control', 'disabled', 'placeholder' => __('Due Date')]) !!}
  </div>
  <div class="form-group col-12">
    {{ Form::label('frequency', __('Cycle Interval'), ['class' => 'col-form-label']) }}
    <div class="d-flex">
      {!! Form::text('rrule_interval', 1, ['class' => 'form-control', 'placeholder' => 'Cycle Interval']) !!}
      {!! Form::select('frequency', ['Day' => 'Day(s)', 'Week' => 'Week(s)', 'Month' => 'Month(s)'], null, ['class' => 'form-control globalOfSelect2']) !!}
    </div>
  </div>

  <div class="form-group col-12">
    {{ Form::label('rrule_cycle_count', __('Cycle Count'), ['class' => 'col-form-label']) }}
    {!! Form::text('rrule_cycle_count', null, ['class' => 'form-control', 'placeholder' => 'Cycle Count']) !!}
  </div> --}}
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
      $('[name="estimated_cost"]').val(estimatedCost);
    }else{
      $('[name="estimated_cost"]').val('');
    }
  })
</script>
