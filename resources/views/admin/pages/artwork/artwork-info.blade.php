<div class="d-flex justify-content-start align-items-center">
  <div class="avatar-wrapper">
    <div class="avatar avatar-{{$user->is_online ? 'online' : 'offline'}} u-avatar-{{$user->id}} avatar-sm me-3 "><img src="{{ $user->featured_image }}" alt="Avatar" class="rounded-circle">
    </div>
  </div>
  <div class="d-flex flex-column">
    <span class="text-body text-truncate">
      <span class="fw-semibold"><a href="{{ route('admin.artworks.show', $user->id) }}"> {{ $user->name }}</a></span>
    </span>
  </div>
</div>
