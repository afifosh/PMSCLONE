@extends('admin/layouts/layoutMaster')

@section('title', 'User Profile - Connections')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('content')
{{-- <h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Connections
</h4> --}}

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
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.companies.show', $company) }}"><i class='ti ti-user-check ti-xs me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='ti ti-users ti-xs me-1'></i> Users</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.companies.show', ['company' => $company, 'tab' => 'invitations']) }}"><i class='ti ti-link ti-xs me-1'></i> Invitations</a></li>
      <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class='ti ti-layout-grid ti-xs me-1'></i> Connections</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->

<!-- Connection Cards -->
<div class="row g-4">
  @forelse ($company->users as $user)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <div class="dropdown btn-pinned">
            <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
            </ul>
          </div>
          <div class="mx-auto my-3">
            <img src="{{ $user->avatar }}" alt="Avatar Image" class="rounded-circle w-px-100" />
          </div>
          <h4 class="mb-1 card-title">{{ $user->full_name }}</h4>
          <span class="pb-1">{{ $user->email }}</span>
          <div class="d-flex align-items-center justify-content-center my-3 gap-2">
            @forelse ($user->roles as $role)
              <a href="javascript:;" class="me-1"><span class="badge bg-label-warning">{{ $role->name}}</span></a>
            @empty
            @endforelse
          </div>

          <div class="d-flex align-items-center justify-content-around my-3 py-1">
            <div>
              <h4 class="mb-0">18</h4>
              <span>Projects</span>
            </div>
            <div>
              <h4 class="mb-0">834</h4>
              <span>Tasks</span>
            </div>
            <div>
              <h4 class="mb-0">129</h4>
              <span>Connections</span>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-center">
            <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3"><i class="ti-xs me-1 ti ti-user-check me-1"></i>{{ ucfirst($user->status)}}</a>
            <a href="javascript:;" class="btn btn-label-secondary btn-icon"><i class="ti ti-mail ti-sm"></i></a>
          </div>
        </div>
      </div>
    </div>
  @empty

  @endforelse

</div>
<!--/ Connection Cards -->
@endsection
