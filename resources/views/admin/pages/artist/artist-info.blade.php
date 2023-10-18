<div class="d-flex justify-content-start align-items-center">
  <div class="avatar-wrapper">
    <div class="avatar avatar-{{$user->is_online ? 'online' : 'offline'}} u-avatar-{{$user->id}} avatar-sm me-3 "><img src="{{ $user->avatar }}" alt="Avatar" class="rounded-circle">
    </div>
  </div>
  <div class="d-flex flex-column">
    <span class="text-body text-truncate">
      <span class="fw-semibold"><a href="{{ route('admin.artists.show', $user->id) }}"> {{ $user->full_name }}</a></span>
    </span>
    <small class="text-muted">{{ $user->email }}</small>
  </div>
</div>
