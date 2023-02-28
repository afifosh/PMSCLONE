<div class="card-body pb-0">
  <ul class="timeline ms-1 mb-0">
    @forelse ($logs as $log)
      <li class="timeline-item timeline-item-transparent">
        <span class="timeline-point timeline-point-primary"></span>
        <div class="timeline-event">
          <div class="timeline-header">
            <h6 class="mb-0">{{ $log->log }}</h6>
            <small class="text-muted">{{$log->created_at->diffForHumans()}}</small>
          </div>
          @if($log->actioner)
          <p class="mb-2"> {{ $log->log}} by {{ $log->actioner->full_name }} @ {{formatDateTime($log->created_at)}}</p>
          <div class="d-flex flex-wrap">
            <div class="avatar me-2">
              <img src="{{ $log->actioner->avatar }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="ms-1">
              <h6 class="mb-0">{{ $log->actioner->full_name }}</h6>
              <span>{{ $log->actioner->email }}</span>
            </div>
          </div>
          @else
          <p class="mb-2"> {{ $log->log}} @ {{formatDateTime($log->created_at)}}</p>
          @endif
        </div>
      </li>
    @empty
    @endforelse
  </ul>
</div>
