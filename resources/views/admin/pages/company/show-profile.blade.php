@extends('admin/layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
@endsection

@section('content')
@include('admin.pages.company.header', ['tab' => 'profile'])
<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase">About</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-user"></i><span class="fw-bold mx-2">Full Name:</span> <span>John Doe</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-check"></i><span class="fw-bold mx-2">Status:</span> <span>Active</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-crown"></i><span class="fw-bold mx-2">Role:</span> <span>Developer</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Country:</span> <span>USA</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-file-description"></i><span class="fw-bold mx-2">Languages:</span> <span>English</span></li>
        </ul>
        <small class="card-text text-uppercase">Contacts</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-phone-call"></i><span class="fw-bold mx-2">Contact:</span> <span>(123) 456-7890</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-brand-skype"></i><span class="fw-bold mx-2">Skype:</span> <span>john.doe</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-mail"></i><span class="fw-bold mx-2">Email:</span> <span>john.doe@example.com</span></li>
        </ul>
        <small class="card-text text-uppercase">Teams</small>
        <ul class="list-unstyled mb-0 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-brand-angular text-danger me-2"></i>
            <div class="d-flex flex-wrap"><span class="fw-bold me-2">Backend Developer</span><span>(126 Members)</span></div>
          </li>
          <li class="d-flex align-items-center"><i class="ti ti-brand-react-native text-info me-2"></i>
            <div class="d-flex flex-wrap"><span class="fw-bold me-2">React Developer</span><span>(98 Members)</span></div>
          </li>
        </ul>
      </div>
    </div>
    <!--/ About User -->
    <!-- Profile Overview -->
    <div class="card mb-4">
      <div class="card-body">
        <p class="card-text text-uppercase">Overview</p>
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-check"></i><span class="fw-bold mx-2">Task Compiled:</span> <span>13.5k</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-layout-grid"></i><span class="fw-bold mx-2">Projects Compiled:</span> <span>146</span></li>
          <li class="d-flex align-items-center"><i class="ti ti-users"></i><span class="fw-bold mx-2">Connections:</span> <span>897</span></li>
        </ul>
      </div>
    </div>
    <!--/ Profile Overview -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Activity Timeline -->
    <div class="card card-action mb-4">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0">Activity Timeline</h5>
        <div class="card-action-element">
          <div class="dropdown">
            <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="javascript:void(0);">Share timeline</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pb-0">
        <ul class="timeline ms-1 mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">Client Meeting</h6>
                <small class="text-muted">Today</small>
              </div>
              <p class="mb-2">Project meeting with john @10:15am</p>
              <div class="d-flex flex-wrap">
                <div class="avatar me-2">
                  <img src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar" class="rounded-circle" />
                </div>
                <div class="ms-1">
                  <h6 class="mb-0">Lester McCarthy (Client)</h6>
                  <span>CEO of Infibeam</span>
                </div>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-success"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">Create a new project for client</h6>
                <small class="text-muted">2 Day Ago</small>
              </div>
              <p class="mb-0">Add files to new design folder</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-danger"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">Shared 2 New Project Files</h6>
                <small class="text-muted">6 Day Ago</small>
              </div>
              <p class="mb-2">Sent by Mollie Dixon <img src="{{ asset('assets/img/avatars/4.png') }}" class="rounded-circle me-3" alt="avatar" height="24" width="24"></p>
              <div class="d-flex flex-wrap gap-2 pt-1">
                <a href="javascript:void(0)" class="me-3">
                  <img src="{{asset('assets/img/icons/misc/doc.png') }}" alt="Document image" width="15" class="me-2">
                  <span class="fw-semibold text-heading">App Guidelines</span>
                </a>
                <a href="javascript:void(0)">
                  <img src="{{asset('assets/img/icons/misc/xls.png') }}" alt="Excel image" width="15" class="me-2">
                  <span class="fw-semibold text-heading">Testing Results</span>
                </a>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-0">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">Project status updated</h6>
                <small class="text-muted">10 Day Ago</small>
              </div>
              <p class="mb-0">Woocommerce iOS App Completed</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <!--/ Activity Timeline -->
    <div class="row">
      <!-- Connections -->
      <div class="col-lg-12 col-xl-6">
        <div class="card card-action mb-4">
          <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0">Connections</h5>
            <div class="card-action-element">
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="javascript:void(0);">Share connections</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Cecilia Payne</h6>
                      <small class="text-muted">45 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-label-primary btn-icon btn-sm"><i class="ti ti-user-check ti-xs"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Curtis Fletcher</h6>
                      <small class="text-muted">1.32k Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-primary btn-icon btn-sm"><i class="ti ti-user-x ti-xs"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/avatars/10.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Alice Stone</h6>
                      <small class="text-muted">125 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-primary btn-icon btn-sm"><i class="ti ti-user-x ti-xs"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/avatars/7.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Darrell Barnes</h6>
                      <small class="text-muted">456 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-label-primary btn-icon btn-sm"><i class="ti ti-user-check ti-xs"></i></button>
                  </div>
                </div>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Eugenia Moore</h6>
                      <small class="text-muted">1.2k Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-label-primary btn-icon btn-sm"><i class="ti ti-user-check ti-xs"></i></button>
                  </div>
                </div>
              </li>
              <li class="text-center">
                <a href="javascript:;">View all connections</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--/ Connections -->
      <!-- Teams -->
      <div class="col-lg-12 col-xl-6">
        <div class="card card-action mb-4">
          <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0">Teams</h5>
            <div class="card-action-element">
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="javascript:void(0);">Share teams</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/icons/brands/react-label.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">React Developers</h6>
                      <small class="text-muted">72 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-danger">Developer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/icons/brands/support-label.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Support Team</h6>
                      <small class="text-muted">122 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-primary">Support</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/icons/brands/figma-label.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">UI Designers</h6>
                      <small class="text-muted">7 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-info">Designer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/icons/brands/vue-label.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Vue.js Developers</h6>
                      <small class="text-muted">289 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-danger">Developer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-2">
                      <img src="{{ asset('assets/img/icons/brands/twitter-label.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2 ms-1">
                      <h6 class="mb-0">Digital Marketing</h6>
                      <small class="text-muted">24 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-secondary">Marketing</span></a>
                  </div>
                </div>
              </li>
              <li class="text-center">
                <a href="javascript:;">View all teams</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--/ Teams -->
    </div>
    <!-- Projects table -->
    {{-- <div class="card mb-4">
      <div class="card-datatable table-responsive">
        {{$dataTable->table()}}
      </div>
    </div> --}}
    <div class="card  mb-4">
      <div class="card-header">
        <h4>Comapny Invitations </h4>
      </div>
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
    <!--/ Projects table -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
