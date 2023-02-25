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
              <h4>{{ $draft_rfp->name}}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> <a href="{{route('admin.programs.show', $draft_rfp->program->id)}}">{{ $draft_rfp->program->name ?? '-' }}</a>
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> Created At {{formatDateTime($draft_rfp->created_at)}}</li>
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
      <li class="nav-item"><a class="nav-link {{$tab == 'profile' ? 'active disabled' : ''}}" href="{{ route('admin.draft-rfps.show', ['draft_rfp' => $draft_rfp])}}"><i class='ti-xs ti ti-user-check me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'users' ? 'active disabled' : ''}}" href="{{ route('admin.draft-rfps.users_tab', ['draft_rfp' => $draft_rfp]) }}"><i class='ti-xs ti ti-users me-1'></i> Users</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'files' ? 'active disabled' : ''}}"  href="{{ route('admin.draft-rfps.files.index', ['draft_rfp' => $draft_rfp]) }}"><i class='ti-xs ti ti-layout-grid me-1'></i> Files</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'shared-files' ? 'active disabled' : ''}}"  href="{{ route('admin.draft-rfps.files.shares.index', ['draft_rfp' => $draft_rfp, 'file' => 'all']) }}"><i class='fa fa-retweet me-1'></i>Shared Files</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'files-activity' ? 'active disabled' : ''}}"  href="{{ route('admin.draft-rfps.files_activity', ['draft_rfp' => $draft_rfp]) }}"><i class='ti-xs ti ti-layout-grid me-1'></i> Files Activity</a></li>
      {{-- <li class="nav-item"><a class="nav-link {{$tab == 'draft-activity' ? 'active disabled' : ''}}"  href="{{ route('admin.draft-rfps.activity_tab', ['draft_rfp' => $draft_rfp]) }}"><i class='ti-xs ti ti-layout-grid me-1'></i> Draft Activity</a></li> --}}
    </ul>
  </div>
</div>
<!--/ Navbar pills -->
