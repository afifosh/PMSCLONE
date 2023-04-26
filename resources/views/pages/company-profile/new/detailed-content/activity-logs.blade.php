@extends('layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
@endsection

<!-- Page -->
@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />
@endsection


@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/block-ui/block-ui.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-profile.js') }}"></script>
    <script src="{{ asset('assets/js/custom/company-profile-page.js') }}"></script>
    <script src="{{ asset('assets/js/custom/toastr-helpers.js') }}"></script>
@endsection

@section('content')
@include('pages.company-profile.new.detailed-content-header', ['tab' => 'activity-logs'])
<div class="card">
  <div class="card-body pb-0">
      <ul class="timeline ms-1 mb-0">
          @forelse ($logs as $log)
              <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point timeline-point-primary"></span>
                  <div class="timeline-event">
                      <div class="timeline-header">
                          <h6 class="mb-0">
                            {{ $log->modifier->fullName }}
                            <span class="fst-italic">{{ $log->is_update ? 'updated' : 'created' }}</span>
                            {{ $log->modifiable_type }}
                          </h6>
                          <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                      </div>
                      @if ($log->actioner)
                          <p class="mb-2"> {{ $log->log }} by {{ $log->actioner->full_name }} @
                              {{ formatDateTime($log->created_at) }}
                              @if ($log->data['ip'])
                                  from {{ $log->data['ip'] }}
                              @endif
                          </p>
                          <div class="d-flex flex-wrap">
                              <div class="avatar me-2">
                                  <img src="{{ $log->actioner->avatar }}" alt="Avatar" class="rounded-circle" />
                              </div>
                              <div class="ms-1">
                                  <h6 class="mb-0">{{ $log->actioner->full_name }}</h6>
                                  <span>{{ $log->actioner->email }}</span>
                              </div>
                          </div>
                      @else
                          <p class="mb-2"> At @ {{ formatDateTime($log->created_at) }}</p>
                      @endif
                      @if($log->approvals->isNotEmpty())
                        <h5>Approvers</h5>
                        <div class="d-flex align-items-center avatar-group">
                          @forelse ($log->approvals as $approval)
                            <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                              aria-label="{{$approval->approver->fullName}}" data-bs-original-title="{{$approval->approver->fullName}}">
                              <img src="{{$approval->approver->avatar}}" alt="Avatar" class="rounded-circle">
                            </div>
                          @empty
                          @endforelse
                        </div>
                      @endif
                      @if($log->disapprovals->isNotEmpty())
                        <h5>Rejected By</h5>
                        <div class="d-flex align-items-center avatar-group">
                          @forelse ($log->disapprovals as $disapproval)
                            <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                              aria-label="{{$disapproval->disapprover->fullName}}" data-bs-original-title="{{$disapproval->disapprover->fullName}}">
                              <img src="{{$disapproval->disapprover->avatar}}" alt="Avatar" class="rounded-circle">
                            </div>
                          @empty
                          @endforelse
                        </div>
                      @endif
                  </div>
              </li>
          @empty
          @endforelse
      </ul>
    <div class="row">
      <div class="col-12">
        <div class="card-footer d-flex justify-content-end">
          {{$logs->links()}}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
