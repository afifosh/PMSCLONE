<div class="d-flex justify-content-start align-items-center user-name mb-4">
  <div class="avatar-wrapper">
    <div class="avatar me-2"><i class="ti ti-license mb-2 ti-xl"></i></div>
  </div>
  <div class="d-flex flex-column">
    <span class="fw-medium mb-1">{{ $contract->subject }}</span>
    <small class="text-muted mb-1">{{ $contract->program->name }}</small>
    <span class="badge bg-label-{{$contract->getStatusColor()}} me-auto">{{$contract->status}}</span>
  </div>
</div>
<div class="row mb-4">
    <div class="nav-align-top">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link {{request()->tab == null || request()->tab == 'summary' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-summary" aria-controls="navs-top-summary" aria-selected="true">Summary</button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{request()->tab == 'activities' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-activities" aria-controls="navs-top-activities" aria-selected="false">Activities</button>
        </li>
        <li class="nav-item" >
          <button type="button" class="nav-link {{request()->tab == 'comments' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comments" aria-controls="navs-top-comments" aria-selected="false">Comments</button>
        </li>
      </ul>
      <div class="tab-content p-0">
        <div class="tab-pane fade {{request()->tab == null || request()->tab == 'summary' ? 'show active' : ''}}" id="navs-top-summary" role="tabpanel">
          {{-- @includeWhen(request()->tab == null || request()->tab == 'summary','admin.pages.projects.tasks.show-summary') --}}
        </div>
        <div class="tab-pane fade {{request()->tab == 'activities' ? 'show active' : ''}}" id="navs-top-activities" role="tabpanel">
          {{-- @includeWhen(request()->tab == 'activities', 'admin.pages.projects.tasks.show-activities') --}}
        </div>
        <div class="tab-pane fade {{request()->tab == 'comments' ? 'show active' : ''}}" id="navs-top-comments" role="tabpanel">
          {{-- @includeWhen(request()->tab == 'comments', 'admin.pages.projects.tasks.show-comments') --}}

          @php
          $contractAudits = \App\Models\Audit::where('auditable_id', $phase->id)
                                ->where('auditable_type', get_class($phase))
                                ->orderBy('created_at', 'desc')
                                ->get();
                              
                                @endphp
<div class="card-body pb-0 mt-4">
  <ul class="timeline mb-0">
      @foreach($contractAudits as $audit)
      <li class="timeline-item timeline-item-transparent">
          <span class="timeline-point {{ $audit->event == 'created' ? 'timeline-point-success' : ($audit->event == 'updated' ? 'timeline-point-primary' : 'timeline-point-danger') }}"></span>
          <div class="timeline-event">
              <div class="timeline-header border-bottom mb-3">
                  <h6 class="mb-0">{{ $audit->created_at->format('jS F Y') }}</h6>
                  <span class="text-muted">{{ $audit->created_at->format('h:i A') }}</span>
              </div>
              <div class="d-flex justify-content-between flex-wrap mb-2">
                  <div class="d-flex align-items-center">
                      <p>{!! $audit->renderFieldAudit() !!}</p>
                  </div>
                  <div>
                      {{-- <span class="text-muted">{{ $audit->created_at->format('h:i A') }}</span> --}}
                  </div>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap pb-0 px-0">
                <div class="d-flex align-items-center">
                  <img src="{{ $audit->user->avatar  }}" class="rounded-circle me-3" alt="avatar" height="24" width="24">
                  <div class="user-info">
                    <p class="my-0">{{ $audit->user->name ?? 'Unknown' }}</p>
                    
                  </div>
                </div>
                
              </div>
          </div>
      </li>
      @endforeach
  </ul>
</div>


    
        </div>
      </div>
    </div>
</div>
@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.update', ['project' => 'project', 'contract' => $contract, 'phase' => $phase->id, 'stage' => $stage]],
      'method' => 'PUT',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.store',  ['project' => 'project', 'contract' => $contract, 'stage' => $stage]], 'method' => 'POST']) !!}
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
    {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label']) }}
    @php
    $selectedStageId = isset($stage->id) ? $stage->id : null;
    @endphp
    {!! Form::select('stage_id', $stages, $selectedStageId, ['class' => 'form-control select2', 'placeholder' => 'Select Stage']) !!}
    
  </div>
  <div class="form-group col-6">
    {{ Form::label('estimated_cost', __('Estimated Cost'), ['class' => 'col-form-label']) }}
    <div class="dropdown open d-inline">
      <span data-bs-toggle="dropdown" aria-haspopup="true">
          <i class="fas fa-calculator"></i>
      </span>
      <div class="dropdown-menu p-3">
        <div class="mb-3" data-content="percent-cal">
          <label for="percent-value" class="form-label">Percentage (Total: {{$contract->value}})</label>
          <input type="number" name="percent-value" id="percent-value" data-balance="{{$contract->value}}" class="form-control" placeholder="10%">
        </div>
      </div>
    </div>
    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'placeholder' => __('Estimated Cost')]) !!}
  </div>
  {{-- taxes --}}
  <div class="col-6">
    {{ Form::label('phase_taxes', __('Tax'), ['class' => 'col-form-label']) }}
    <select class="form-select globalOfSelect2" name="phase_taxes[]" multiple data-placeholder="{{__('Select Tax')}}" data-allow-clear=true>
      @forelse ($tax_rates->where('is_retention', false) as $tax)
        <option @selected($phase->taxes->contains($tax)) value="{{$tax->id}}" data-amount="{{$tax->amount}}" data-type={{$tax->type}}>{{$tax->name}} (
          @if($tax->type != 'Percent')
            @cMoney($tax->amount, $phase->contract->currency, true)
          @else
            {{$tax->amount}}%
          @endif
        )</option>
      @empty
      @endforelse
    </select>
  </div>
  <!-- New adjustment amount field -->
  <div class="form-group col-6">
    {{ Form::label('adjustment_amount', __('Adjustment Amount'), ['class' => 'col-form-label']) }}
    {!! Form::number('adjustment_amount', null, ['class' => 'form-control', 'placeholder' => __('Adjustment Amount')]) !!}
  </div>  
  {{-- total cost --}}
  <div class="form-group col-6">
    {{ Form::label('total_cost', __('Total Cost'), ['class' => 'col-form-label']) }}
    {!! Form::number('total_cost', null, ['class' => 'form-control', 'placeholder' => __('Total Cost'), 'disabled', 'data-max' => $max_amount])!!}
  </div>
  {{-- start date --}}
  <div class="form-group col-6">
    {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('start_date', $phase->start_date, ['class' => 'form-control flatpickr', 'id' => 'start_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $contract->start_date .'", "maxDate":"'. $contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Start Date')]) !!}
  </div>
  {{-- due date --}}
  <div class="form-group col-6">
    {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
    {!! Form::date('due_date', $phase->due_date, ['class' => 'form-control flatpickr', 'id' => 'phase_end_date', 'data-flatpickr' => '{"altFormat": "F j, Y", "minDate":"'. $contract->start_date .'", "maxDate":"'. $contract->end_date .'", "dateFormat": "Y-m-d"}', 'placeholder' => __('Due Date')]) !!}
  </div>
  <div class="col-12 mt-2">
    <div class="form-check">
      <input class="form-check-input" name="calc_end_date" type="checkbox" id="cal-phase-end-date">
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
          {!! Form::select('cal_end_date_unit', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], null, ['class' => 'cont-add-unit cal-phase-end-date input-group-text form-select globalOfSelect2']) !!}
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

<div class="mt-3 d-flex justify-content-between">
  <div class="phase-editing-users">
  </div>
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

  function calculateTotalCost()
  {
    const estimatedCost = $('[name="estimated_cost"]').val();
    const adjustmentInputValue = $('[name="adjustment_amount"]').val();
    const adjustmentAmount = adjustmentInputValue ? parseFloat(adjustmentInputValue) : 0;

    if (isNaN(adjustmentAmount)) {
        adjustmentAmount = 0;
    }
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
      totalCost += (totalCost * percentagTax) / 100;
      totalCost += fixedTax;
    }

    // Incorporate the adjustment amount
    totalCost += adjustmentAmount;

    totalCost = totalCost.toFixed(3);
    $('[name="total_cost"]').val(totalCost);
    validateTotalCost();
  }

  $(document).on('change keyup', '[name="adjustment_amount"]', function(){
    calculateTotalCost();
  });

  $(document).on('change', '[name="total_cost"]', function(){
    validateTotalCost();
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
