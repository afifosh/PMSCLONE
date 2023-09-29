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
                  <div class="px-3 timeline-event bg-light mt-2" style="opacity: 0.8;">
                      <div class="timeline-header">
                          <h6 class="mb-0">
                            {{ $log->modifier->fullName }}
                            <span class="fst-italic bg-label-success p-1">{{ $log->is_update ? 'Updated' : 'Added' }}</span>
                            {{ $log->modifiable_type::getModelName() }}
                            @php
                                $status = $log->approvers_required <= count($log->approvals ?? []) ? 'approved' : (count($log->disapprovals ?? []) ? 'rejected' : 'pending');
                            @endphp
                            <span class="badge bg-label-{{getCompanyStatusColor($status)}}">{{ ucfirst($status) }}</span>
                          </h6>
                          <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                      </div>
                      <p class="mb-2"> At @ {{ formatDateTime($log->created_at) }}</p>
                      @if ($log->modifiable_type != 'App\Models\UploadedKycDoc')
                        {{-- <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-details-{{$log->id}}" aria-expanded="false" aria-controls="collapse-details-{{$log->id}}">
                          Details
                        </button>
                        @if ($log->approvals->isNotEmpty())
                          <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-details-approvals-{{$log->id}}" aria-expanded="false" aria-controls="collapse-details-approvals-{{$log->id}}">
                            Approved By
                          </button>
                        @endif
                        <div class="collapse" id="collapse-details-{{$log->id}}">
                          <div class="card card-body">
                            <div class="row">
                              @forelse ($log->modifications as $i => $modification)
                                @if (isset(array_flip($log->modifiable::getFields())[$i]))
                                  <div class="col-6 my-1">
                                    <div class="fw-bold">
                                      {{array_flip($log->modifiable::getFields())[$i] ?? 'N/A'}}
                                    </div>
                                    <span class="fst-italic d-flex justify-content-between">
                                      <span>
                                        @if ($modification['original'])
                                          <span class="text-decoration-line-through">{{is_array($modification['original']) ? implode(', ', $modification['original']) : $modification['original'] ?? 'N/A'}}</span>
                                        @endif
                                        {{is_array($modification['modified']) ? implode(', ', $modification['modified']) : $modification['modified'] ?? 'N/A'}}
                                      </span>
                                    </span>
                                  </div>
                                @endif
                              @empty
                              @endforelse
                            </div>
                          </div>
                        </div>

                        <div class="collapse" id="collapse-details-approvals-{{$log->id}}">
                          <div class="card card-body">
                            <div class="row">
                              <div class="d-flex align-items-center avatar-group">
                                @forelse ($log->approvals as $approval)
                                  <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    aria-label="{{$approval->approver->fullName}}" data-bs-original-title="{{$approval->approver->fullName}}">
                                    <img src="{{$approval->approver->avatar}}" alt="Avatar" class="rounded-circle">
                                  </div>
                                @empty
                                @endforelse
                              </div>
                            </div>
                          </div>
                        </div> --}}

                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-detail-tab-{{$log->id}}" data-bs-toggle="pill" data-bs-target="#pills-detail-{{$log->id}}" type="button" role="tab" aria-controls="pills-detail-{{$log->id}}" aria-selected="true">Details</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-approvals-tab-{{$log->id}}" data-bs-toggle="pill" data-bs-target="#pills-approvals-{{$log->id}}" type="button" role="tab" aria-controls="pills-approvals-{{$log->id}}" aria-selected="false">Approvals</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-rejections-tab-{{$log->id}}" data-bs-toggle="pill" data-bs-target="#pills-rejections-{{$log->id}}" type="button" role="tab" aria-controls="pills-rejections-{{$log->id}}" aria-selected="false">Rejections</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-chat-tab-{{$log->id}}" data-bs-toggle="pill" data-bs-target="#pills-chat-{{$log->id}}" type="button" role="tab" aria-controls="pills-chat-{{$log->id}}" aria-selected="false">Chat</button>
                          </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                          <div class="tab-pane show active" id="pills-detail-{{$log->id}}" role="tabpanel" aria-labelledby="pills-detail-tab-{{$log->id}}">
                            <div class="row">
                              @forelse ($log->modifications as $i => $modification)
                                @if (isset(array_flip($log->modifiable_type::getFields())[$i]))
                                  <div class="col-6 my-1">
                                    <div class="fw-bold">
                                      {{array_flip($log->modifiable_type::getFields())[$i] ?? 'N/A'}}
                                    </div>
                                    <span class="fst-italic d-flex justify-content-between">
                                      <span>
                                        @if ($modification['original'])
                                          <span class="text-decoration-line-through">{{is_array($modification['original']) ? implode(', ', $modification['original']) : $modification['original'] ?? 'N/A'}}</span><br>
                                        @endif
                                        {{is_array($modification['modified']) ? implode(', ', $modification['modified']) : $modification['modified'] ?? 'N/A'}}
                                      </span>
                                    </span>
                                  </div>
                                @endif
                              @empty
                              @endforelse
                            </div>
                          </div>
                          <div class="tab-pane fade" id="pills-approvals-{{$log->id}}" role="tabpanel" aria-labelledby="pills-approvals-tab-{{$log->id}}">
                            <div class="row">
                              @forelse ($log->approvals as $approval)
                              <span>{{$approval->approver->fullName}} Approved at <span class="bg-label-success">level {{$loop->iteration}}</span> {{$approval->created_at->diffForHumans()}}</span>
                              <br>
                              @empty
                              @endforelse
                            </div>
                          </div>
                          <div class="tab-pane fade" id="pills-rejections-{{$log->id}}" role="tabpanel" aria-labelledby="pills-rejections-tab-{{$log->id}}">
                            <div class="row">
                              @forelse ($log->disapprovals as $disapproval)
                              <span>{{$disapproval->disapprover->fullName}} Rejected changes at <span class="bg-label-danger">level {{count($log->approvals) + $loop->iteration}}</span> {{$disapproval->created_at->diffForHumans()}}</span>
                              <br>
                              <span class="fw-bold">With Reason</span>
                              <span class="bg-label-warning">{{$disapproval->reason}}</span>
                              @empty
                              @endforelse
                            </div>
                          </div>
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
