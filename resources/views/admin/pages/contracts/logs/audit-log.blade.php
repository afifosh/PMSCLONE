@if($log->event == 'created')
  @forelse ($log->new_values as $key => $value)
    @continue(in_array($key, ['id', 'contract_id']))
    {{ ucwords($key) }}: <span class="badge bg-label-success">{{ $value }}</span><br>
  @empty
  @endforelse
@elseif($log->event == 'updated')
  @forelse ($log->new_values as $key => $value)
    @continue(in_array($key, ['id', 'contract_id']))
    {{ ucwords($key) }}:  <span class="badge bg-label-danger text-decoration-line-through"> {{$log->old_values[$key]}} </span><span class="badge bg-label-success">{{ $value }}</span><br>
  @empty
  @endforelse
@elseif($log->event == 'deleted')
  @forelse ($log->old_values as $key => $value)
    @continue(in_array($key, ['id', 'contract_id']))
    {{ ucwords($key) }}: <span class="badge bg-label-danger">{{ $value }}</span><br>
  @empty
  @endforelse
@endif
