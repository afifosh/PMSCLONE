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
      {{-- <!--begin::Details-->
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

      <div class="separator"></div> --}}

      <!--begin::Nav-->
      <ul class="nav nav-stretch nav-line-tabs border-transparent fs-6 fw-semibold">
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'invoice' ? 'active' : ''}}" href="{{route('admin.invoices.edit', [$invoice])}}"><i class='ti ti-user-check ti-xs me-1'></i> Invoice</a></li>
      {{-- <li class="nav-item"><a class="nav-link py-3 {{$tab == 'payments' ? 'active' : ''}}" href="{{route('admin.contracts.payments.index', [$contract])}}"><i class='ti ti-link ti-xs me-1'></i> Payments</a></li> --}}
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'uploaded-documents' ? 'active' : ''}}" href="{{route('admin.invoices.uploaded-documents.index', [$invoice])}}"><i class='ti ti-link ti-xs me-1'></i> Uploaded Documents</a></li>
      <li class="nav-item"><a class="nav-link py-3 {{$tab == 'pending-documents' ? 'active' : ''}}" href="{{route('admin.invoices.pending-documents.index', [$invoice])}}"><i class='ti ti-link ti-xs me-1'></i> Pending Documents</a></li>
      </ul>
      <!--end::Nav-->
  </div>
</div>