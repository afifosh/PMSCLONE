<div class="card-body pb-0 mt-4">
    <ul class="timeline mb-0">
        @foreach($contractAudits as $audit)
        <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point {{ $audit->event == 'created' ? 'timeline-point-success' : ($audit->event == 'updated' ? 'timeline-point-warning' : 'timeline-point-danger') }}"></span>
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
                    <img src="{{ $audit->user->avatar ?? '' }}" class="rounded-circle me-3" alt="avatar" height="24" width="24">
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
