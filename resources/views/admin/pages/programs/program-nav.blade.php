<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ asset('assets/img/avatars/14.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ $program->name }}
                @if ($program->parent)
                  (<a href="{{route('admin.programs.show', $program->parent)}}">{{$program->parent->name}}</a>)
                @endif
              </h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> UX Designer
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> Added At {{ formatDateTime($program->created_at)}}</li>
              </ul>
            </div>
            <a href="javascript:void(0)" class="btn btn-primary">
              <i class='ti ti-user-check me-1'></i>Connected
            </a>
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
      <li class="nav-item"><a class="nav-link {{$tab == 'profile' ? 'active' : ''}}" href="{{ route('admin.programs.show', $program)}}"><i class='ti-xs ti ti-user-check me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'rfps' ? 'active' : ''}}" href="{{ route('admin.programs.showDraftRFPs', ['program' => $program->id])}}"><i class='ti-xs ti ti-users me-1'></i> RFP Drafs</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'users' ? 'active' : ''}}" href="{{ route('admin.programs.users.index', ['program' => $program->id])}}"><i class='ti-xs ti ti-users me-1'></i> Users</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->
