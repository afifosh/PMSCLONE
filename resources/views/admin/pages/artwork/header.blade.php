<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $artwork->featured_image }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
            <div class="flex-grow-1 mt-3 mt-sm-5">
              <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                <div class="user-profile-info">
                  <h4>{{ $artwork->name }}
                    @if ($artwork->approval_status == 1)
                        <img width="30px" src="{{ asset('assets/img/pages/verified.png') }}" alt="">
                    @endif</h4>
                    <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                      <li class="list-inline-item">
                        <i class='ti ti-calendar'></i> <b>Joined: </b> {{formatDateTime($artwork->created_at)}}</li>
                    </ul>
                </div>
                <a href="{{route('admin.artworks.edit', $artwork)}}" class="btn btn-primary waves-effect waves-light">
                  <i class="ti ti-check me-1"></i>{{__('Edit Artwork')}}</a>
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
      <li class="nav-item"><a class="nav-link {{$tab == 'profile' ? 'active disabled' : ''}}" href="{{ route('admin.companies.show', $artwork) }}"><i class='ti ti-user-check ti-xs me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'users' ? 'active disabled' : ''}}" href="{{ route('admin.companies.contacts.index', ['company' => $artwork]) }}"><i class='ti ti-users ti-xs me-1'></i> Artworks</a></li>
      <li class="nav-item"><a class="nav-link {{$tab == 'contracts' ? 'active disabled' : ''}}" href="{{ route('admin.companies.contracts.index', ['company' => $artwork]) }}"><i class='ti ti-users ti-xs me-1'></i> Programs</a></li>
    </ul>
  </div>
</div>
