@foreach($notifications as $notification)
<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
  <div class="d-flex">
    <div class="flex-shrink-0 me-3">
      <div class="avatar">
        <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-alert-triangle"></i></span>
      </div>
    </div>
    <div class="flex-grow-1">
      <h6 class="mb-1">Alert!</h6>
      <p class="mb-0">You have logged in from a different device.<br> Device: {{ $notification->data['auth_device'] }}</p>
      {{-- <small class="text-muted">5 days ago</small> --}}
    </div>
    <div class="flex-shrink-0 dropdown-notifications-actions">
      <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
      <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
    </div>
  </div>
</li>
@endforeach
