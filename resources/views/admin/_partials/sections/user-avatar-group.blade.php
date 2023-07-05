<ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
    @forelse ($users as $user)
    @php
        $limit = isset($limit) ? $limit : 0;
    @endphp
      @if ((!$limit || $loop->iteration <= $limit))
        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $user->full_name }}"
            class="avatar avatar-sm pull-up">
            <img class="rounded-circle" src="{{ $user->avatar }}" alt="Avatar">
        </li>
      @else
        <div class="avatar">
          <span class="avatar-initial bg-dark text-light rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="+{{count($users)-$loop->iteration+1}}">+{{count($users)-$loop->iteration+1}}</span>
        </div>
      @break
      @endif
    @empty
    @endforelse
</ul>
