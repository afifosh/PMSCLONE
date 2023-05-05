<div class="d-flex justify-content-start align-items-center">
  <div class="avatar-wrapper">
    <div class="avatar avatar-sm me-3"><img src="{{$user->avatar}}" alt="Avatar" class="rounded-circle">
    </div>
  </div>
  <div class="d-flex flex-column">
    <span class="text-body text-truncate">
      <span class="fw-semibold"><a href="#" class="fw-semibold">{{$user->full_name}}</a></span>
      {{-- {{route('users.show', $user->id)}} --}}
    </span>
    <small class="text-muted">{{$user->email}}</small>
  </div>
</div>
