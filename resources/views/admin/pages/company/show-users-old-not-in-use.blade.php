@extends('admin/layouts/layoutMaster')

@section('title', 'User Profile - Connections')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('content')
@include('admin.pages.company.header', ['tab' => 'users'])
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
