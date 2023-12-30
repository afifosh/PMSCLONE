@if($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Pause Contract')
  <div>Pause Contract</div>
    {{-- <div>From: <span class="bade bg-label-warning p-1"> {{ date("d M, Y", strtotime($changeRequest->data['pause_date'])) }}</span></div>
    <div>Until:
      @if(isset($changeRequest->data['pause_until']))
        @if($changeRequest->data['pause_until'] != 'manual')
          <span class="bade bg-label-success p-1"> {{ date("d M, Y", strtotime($changeRequest->data['pause_until'])) }}</span>
        @else
          <span class="bade bg-label-success p-1"> Manual Resume</span>
        @endif
      @else
        <span class="bade bg-label-success p-1"> Manual Resume</span>
      @endif
    </div> --}}
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Resume')
  <div>Resume Contract</div>
  {{-- <div>Resume On: <span class="bade bg-label-success p-1"> {{ date("d M, Y", strtotime($changeRequest->data['resume_date'])) }}</span></div> --}}
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Termination')
  <div>Terminate Contract</div>
  {{-- <div>Terminate On: <span class="bade bg-label-danger p-1"> {{ date("d M, Y", strtotime($changeRequest->data['termination_date'])) }}</span></div> --}}
@elseif($changeRequest->type == 'Terms')
  @if($changeRequest->old_currency != $changeRequest->new_currency)
    <div class="fw-bold">Currency:</div>
    <div> From: <span class="bade bg-label-warning p-1">{{ $changeRequest->old_currency }}</span></div>
    <div> To: <span class="bade bg-label-success p-1">{{ $changeRequest->new_currency }}</span></div>
  @endif
  @if($changeRequest->old_value != $changeRequest->new_value)
    <div class="fw-bold">Price:</div>
    <div> From: <span class="bade bg-label-warning p-1">{{ $changeRequest->old_value }}</span></div>
    <div> To: <span class="bade bg-label-success p-1">{{ $changeRequest->new_value }}</span></div>
  @endif
  @if($changeRequest->old_end_date != $changeRequest->new_end_date)
    <div class="fw-bold">End Date:</div>
    <div> From: <span class="bade bg-label-warning p-1">{{ date("d M, Y", strtotime($changeRequest->old_end_date)) }}</span></div>
    <div> To: <span class="bade bg-label-success p-1">{{ date("d M, Y", strtotime($changeRequest->new_end_date)) }}</span></div>
  @endif
@elseif($changeRequest->type == 'Lifecycle' && $changeRequest->data['action'] == 'Early Completed')
<div>Early Completed</div>
@endif
