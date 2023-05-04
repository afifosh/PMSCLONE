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
              <h4>John Doe</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> UX Designer
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> Joined April 2021</li>
              </ul>
            </div>
            <div>
              <div class="d-flex justify-content-between">
                <span class="">Setup Progress</span>
                <span>{{auth()->user()->company->step_completed_count}}/5</span>
              </div>
              <div class="progress" style="height:10px; width:300px">
                <div class="progress-bar" role="progressbar" style="width: {{(auth()->user()->company->step_completed_count/5)*100}}%" aria-valuenow="{{(auth()->user()->company->step_completed_count/5)*100}}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
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
      <li class="nav-item"><a class="nav-link {{$tab != 'activity-logs' && $tab != 'approval-requests' ? 'active' : ''}}" href="javascript:void(0);"><i class='ti-xs ti ti-user-check me-1'></i> Details</a></li>
      <li class="nav-item"><a class="nav-link" href="#contact-persons-card"><i class='ti-xs ti ti-users me-1'></i> Contact Persons</a></li>
      <li class="nav-item"><a class="nav-link" href="#addresses-card"><i class='ti-xs ti ti-layout-grid me-1'></i> Addresses</a></li>
      <li class="nav-item"><a class="nav-link" href="#documents-card"><i class='ti-xs ti ti-link me-1'></i> Documents</a></li>
      <li class="nav-item"><a class="nav-link" href="#accounts-card"><i class='ti-xs ti ti-link me-1'></i> Bank Accounts</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'activity-logs' ? 'active' : ''}}" href="{{route('company.profile.activityTimeline')}}"><i class='ti-xs ti ti-link me-1'></i> Activity Timeline</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'approval-requests' ? 'active' : ''}}" href="{{route('company.approval-requests.index')}}"><i class='ti-xs ti ti-link me-1'></i> Approval Requests</a></li>
    </ul>
  </div>
</div>
