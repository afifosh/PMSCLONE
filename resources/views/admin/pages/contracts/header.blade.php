<!--/ Header -->
<style>
  .nav .nav-item .nav-link.active {
    border-bottom: 3px solid var(--bs-primary);
    color: var(--bs-primary);
  }
  .border-dashed {
    border-style: dashed !important;
  }
</style>
<div class="card mb-4">
  <div class="card-body pt-3 pb-0">
      <!--begin::Details-->
      <div class="d-flex flex-wrap flex-sm-nowrap 2">
          <!--begin::Wrapper-->
          <div class="flex-grow-1">
              <!--begin::Head-->
              <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                  <!--begin::Details-->
                  <div class="d-flex flex-column">
                      <!--begin::Status-->
                      <div class="d-flex align-items-center mb-1">
                          <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">{{$contract->subject}}</a>
                          <span class="badge bg-label-{{$contract->getStatusColor()}} me-auto">{{$contract->status}}</span>
                      </div>
                      <!--end::Status-->

                      <!--begin::Description-->
                      <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-muted">
                          {{$contract->description}}
                      </div>
                      <!--end::Description-->
                  </div>
                  <!--end::Details-->
              </div>
              <!--end::Head-->

              <!--begin::Info-->
              <div class="d-flex flex-wrap justify-content-start">
                  <!--begin::Stats-->
                  <div class="d-flex flex-wrap">
                    @if ($contract->start_date)
                        <!--begin::Stat-->
                          <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-3 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-6 fw-bold">{{formatDateTime($contract->start_date)}}</div>
                            </div>
                            <!--end::Number-->

                            <!--begin::Label-->
                            <div class="fw-semibold text-muted">Start Date</div>
                            <!--end::Label-->
                           </div>
                        <!--end::Stat-->
                    @endif
                      <!--begin::Stat-->
                      @if ($contract->end_date)
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-3 mb-3">
                          <!--begin::Number-->
                          <div class="d-flex align-items-center">
                            <div class="fs-6 fw-bold">{{formatDateTime($contract->end_date)}}</div>
                          </div>
                          <!--end::Number-->

                          <!--begin::Label-->
                          <div class="fw-semibold text-muted">End Date</div>
                          <!--end::Label-->
                        </div>
                      @endif
                      <!--end::Stat-->

                      <!--begin::Stat-->
                      @if ($contract->value)
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-3 mb-3">
                          <!--begin::Number-->
                          <div class="d-flex align-items-center">
                            <div class="fs-6 fw-bold">{{$contract->printable_value}}</div>
                          </div>
                          <!--end::Number-->

                          <!--begin::Label-->
                          <div class="fw-semibold text-muted">Value</div>
                          <!--end::Label-->
                        </div>
                      @endif
                      <!--end::Stat-->
                  </div>
                  <!--end::Stats-->

                  <!--begin::Users-->
                  <div class="symbol-group symbol-hover mb-3 pt-3">
                    @include('admin._partials.sections.user-avatar-group', ['users' => $contract->notifiableUsers, 'limit' => 10, 'size' => 'md'])
                  </div>
                  <!--end::Users-->
              </div>
              <!--end::Info-->
          </div>
          <!--end::Wrapper-->
      </div>
      <!--end::Details-->

      <div class="separator"></div>

      <!--begin::Nav-->
      <ul class="nav nav-stretch nav-line-tabs border-transparent fs-6 fw-semibold">
        {{-- <li class="nav-item"><a class="nav-link text-active-primary py-3 active" href="/metronic8/demo1/../demo1/apps/projects/project.html">Overview</li> --}}
          <li class="nav-item"><a class="nav-link py-3 {{$tab == 'overview' ? 'active' : ''}}" href="{{route('admin.contracts.show', [$contract])}}"><i class='ti ti-user-check ti-xs me-1'></i> Overview</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'events' ? 'active' : ''}}" href="{{route('admin.contracts.events.index', [$contract])}}"><i class='ti ti-users ti-xs me-1'></i> Events</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'change-requests' ? 'active' : ''}}" href="{{route('admin.contracts.change-requests.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Change Requests</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'stages' ? 'active' : ''}}" href="{{route('admin.contracts.stages.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Stages</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'invoices' ? 'active' : ''}}" href="{{route('admin.contracts.invoices.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Invoices</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'payments' ? 'active' : ''}}" href="{{route('admin.contracts.payments.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Payments</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'uploaded-documents' ? 'active' : ''}}" href="{{route('admin.contracts.uploaded-documents.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Uploaded Documents</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'pending-documents' ? 'active' : ''}}" href="{{route('admin.contracts.pending-documents.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Pending Documents</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'settings' ? 'active' : ''}}" href="{{route('admin.contracts.settings.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Settings</a></li>
      </ul>
      <!--end::Nav-->
  </div>
</div>
