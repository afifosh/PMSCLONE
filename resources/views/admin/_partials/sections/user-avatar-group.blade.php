<ul class="list-unstyled d-flex align-items-center avatar-group mb-0">';
    @forelse ($users as $user)
        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $user->full_name }}"
            class="avatar avatar-sm pull-up">
            <img class="rounded-circle" src="{{ $user->avatar }}" alt="Avatar">
        </li>
    @empty
    @endforelse
</ul>
