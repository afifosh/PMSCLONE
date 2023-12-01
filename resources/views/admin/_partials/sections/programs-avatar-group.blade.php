<ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
  @forelse ($programs as $program)
  @php
      $limit = isset($limit) ? $limit : 0;
  @endphp
    @if ((!$limit || $loop->iteration <= $limit))
      <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $program->name }}"
          class="avatar avatar-{{isset($size) ? $size : 'sm'}} pull-up">
          <img class="rounded-circle" src="{{ $program->avatar }}" alt="Avatar">
      </li>
    @else
      <div class="avatar">
        <span class="avatar-initial bg-dark text-light rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="+{{count($programs)-$loop->iteration+1}}">+{{count($programs)-$loop->iteration+1}}</span>
      </div>
    @break
    @endif
  @empty
  @endforelse
</ul>
