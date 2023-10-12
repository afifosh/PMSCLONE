<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $company->avatar }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ $company->name }}
              @if ($company->approval_status == 1)
                  <img width="30px" src="{{ asset('assets/img/pages/verified.png') }}" alt="">
              @endif</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  @if($company->type == 'Company')
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-skyscraper" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                      <path d="M3 21l18 0"></path>
                      <path d="M5 21v-14l8 -4v18"></path>
                      <path d="M19 21v-10l-6 -4"></path>
                      <path d="M9 9l0 .01"></path>
                      <path d="M9 12l0 .01"></path>
                      <path d="M9 15l0 .01"></path>
                      <path d="M9 18l0 .01"></path>
                    </svg>
                  @elseif($company->type == 'Person')
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                      <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                      <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                    </svg>
                  @endif
                  {{$company->type}}
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> <b>Joined: </b> {{formatDateTime($company->created_at)}}</li>
              </ul>
            </div>
            {{-- <div>
              <div class="d-flex justify-content-between">
                <span class="">Setup Progress</span>
                <span>{{$company->step_completed_count}}/5</span>
              </div>
              <div class="progress" style="height:10px; width:300px">
                <div class="progress-bar" role="progressbar" style="width: {{($company->step_completed_count/5)*100}}%" aria-valuenow="{{($company->step_completed_count/5)*100}}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div> --}}
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
      <li class="nav-item"><a class="nav-link {{$tab == 'profile' ? 'active disabled' : ''}}" href="{{ route('admin.companies.show', $company) }}"><i class='ti ti-user-check ti-xs me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'users' ? 'active disabled' : ''}}" href="{{ route('admin.companies.contacts.index', ['company' => $company]) }}"><i class='ti ti-users ti-xs me-1'></i> Contacts</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'contracts' ? 'active disabled' : ''}}" href="{{ route('admin.companies.contracts.index', ['company' => $company]) }}"><i class='ti ti-users ti-xs me-1'></i> Contracts</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'invitations' ? 'active disabled' : ''}}" href="{{ route('admin.companies.showInvitations', ['company' => $company]) }}"><i class='ti ti-link ti-xs me-1'></i> Invitations</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'invoices' ? 'active' : ''}}" href="{{ route('admin.companies.invoices.index', ['company' => $company]) }}"><i class='ti ti-link ti-xs me-1'></i> Invoices </a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'payments' ? 'active' : ''}}" href="{{ route('admin.companies.payments.index', ['company' => $company]) }}"><i class='ti ti-link ti-xs me-1'></i> Payments </a></li>
    </ul>
  </div>
</div>
