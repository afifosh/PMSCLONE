<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ $contract->subject }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> UX Designer
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> <b>From: </b> {{formatDateTime($contract->start_date)}} <b>To: </b> {{formatDateTime($contract->end_date)}}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Navbar pills -->
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
      <li class="nav-item"><a class="nav-link {{$tab == 'overview' ? 'active disabled' : ''}}" href="{{route('admin.contracts.show', [$contract])}}"><i class='ti ti-user-check ti-xs me-1'></i> Overview</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'events' ? 'active disabled' : ''}}" href="{{route('admin.contracts.events.index', [$contract])}}"><i class='ti ti-users ti-xs me-1'></i> Events</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'phases' ? 'active disabled' : ''}}" href="{{route('admin.contracts.phases.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Phases</a></li>
      <li class="nav-item"><a class="nav-link disabled {{$tab == 'milestones' ? 'active disabled' : ''}}" href="{{route('admin.contracts.settings.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Milestones</a></li>
      <li class="nav-item"><a class="nav-link disabled {{$tab == 'files' ? 'active disabled' : ''}}" href="{{route('admin.contracts.settings.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Files</a></li>
      <li class="nav-item"><a class="nav-link disabled {{$tab == 'activity' ? 'active disabled' : ''}}" href="{{route('admin.contracts.settings.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Activity</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'settings' ? 'active disabled' : ''}}" href="{{route('admin.contracts.settings.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Settings</a></li>
    </ul>
  </div>
</div>
