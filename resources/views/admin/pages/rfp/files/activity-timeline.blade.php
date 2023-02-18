<div class="card-body pb-0">
  <ul class="timeline ms-1 mb-0">
    @forelse ($file->logs as $log)
      <li class="timeline-item timeline-item-transparent ps-4">
        <span class="timeline-point timeline-point-primary"></span>
        <div class="timeline-event">
          <div class="timeline-header">
            <h6 class="mb-0">{{ $log->actioner->full_name }} ( {{ $log->actioner->email }} )</h6>
            <small class="text-muted">{{$log->created_at->diffForHumans()}}</small>
          </div>
          <p class="mb-0">{{ $log->log }}</p>
        </div>
      </li>
    @empty
    @endforelse
  </ul>
</div>
