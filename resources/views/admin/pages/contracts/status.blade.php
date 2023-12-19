@if($status == 'Not started')
  <span class="badge bg-label-secondary">{{ $status }}</span>
@elseif($status == 'Active')
  <span class="badge bg-label-success">{{ $status }}</span>
@elseif($status == 'About To Expire')
  <span class="badge bg-label-warning">{{ $status }}</span>
@elseif($status == 'Expired')
  <span class="badge bg-label-danger">{{ $status }}</span>
@elseif($status == 'Draft')
  <span class="badge bg-label-secondary">{{ $status }}</span>
@elseif($status == 'Terminated')
  <span class="badge bg-label-danger">{{ $status }}</span>
@elseif($status == 'Paused')
  <span class="badge bg-label-warning">{{ $status }}</span>
@endif



