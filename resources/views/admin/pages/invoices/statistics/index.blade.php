@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Invoice Stats')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.companiesByInvoices = {!! json_encode($companiesByInvoices) !!};
  const topInvoicesByValue = {!! json_encode($invoicesByValue) !!};
  const invoicesByDueDate = {!! json_encode($invoicesByDueDate) !!};
  const invoicesByStatus = {!! json_encode($invoicesByStatus) !!}
  const invoicesByDistribution = {!! json_encode($invoicesByDistribution) !!}
</script>
  <script src="{{asset('assets/js/custom/admin-invoices-stats-index.js')}}"></script>
@endsection

@section('content')
  <div class="mt-3  col-12">
    {{-- Companies By Invoices --}}
    <div class="col-12 col-xl-12 mt-2">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Companies By No. Of Invoices</h5>
        </div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <div id="comapaniesByInvoicesChart"></div>
          </div>
          <div class="col-md-6 d-flex justify-content-around align-items-center">
            @forelse ($companiesByInvoices as $compByInoice)
            @continue($compByInoice['percentage'] == 0)
              <div class="d-flex align-items-baseline">
                <span class="text-primary me-2"><i class='ti ti-circle-filled fs-6'></i></span>
                <div>
                  <p class="mb-2">{{$compByInoice['name']}}</p>
                  <h5>{{$compByInoice['percentage']}}%</h5>
                </div>
              </div>
            @empty
            @endforelse
          </div>
        </div>
      </div>
    </div>
    {{-- Companies By Projects End--}}

    {{-- Types graph start --}}
    <div class="row">
      {{-- <div class="col-12 col-xl-6 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Contracts by type')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contracts-by-type"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-6 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Contracts Value by type')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contracts-by-value"></div>
            </div>
          </div>
        </div>
      </div> --}}

      {{-- Invoices By Amount --}}
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="card-title m-0 me-2">{{__('Invoices By Amount')}}</h5>
            <small class="text-muted">Top 5 Invoices By Amount</small>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="topInvoicesByValue"></div>
            </div>
          </div>
        </div>
      </div>
      {{-- End Invoices By Amount --}}

      {{-- Invoices By Due Date --}}
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Invoices By Due Date')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="invoicesByDueDate"></div>
            </div>
          </div>
        </div>
      </div>
      {{-- End Invoices By Due Date --}}

      <!-- Sales by Countries tabs-->
          <div class="col-md-6 col-xl-4 col-xl-4 mt-2">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between pb-2 mb-1">
                <div class="card-title mb-1">
                  <h5 class="m-0 me-2">Invoices Expiring Soon</h5>
                </div>
              </div>
              <div class="card-body">
                <div class="nav-align-top">
                  <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                      <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-ex-30-days" aria-selected="true">30 Days ({{count($expiringInvoicesList->where('due_date', '<', now()->addDays(30)))}})</button>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-ex-60-days" aria-selected="false">60 Days ({{count($expiringInvoicesList->whereBetween('due_date', [now()->addDays(30), now()->addDays(60)]))}})</button>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-ex-90-days" aria-selected="false">90 Days ({{count($expiringInvoicesList->whereBetween('due_date', [now()->addDays(60), now()->addDays(90)]))}})</button>
                    </li>
                  </ul>
                  <div class="tab-content pb-0">
                    <div class="tab-pane fade show active" id="navs-ex-30-days" role="tabpanel">
                      <ul class="timeline timeline-advance timeline-advance mb-2 pb-1">
                        @forelse ($expiringInvoicesList->where('due_date', '<', now()->addDays(30))  as $ec_30)
                        <li class="timeline-item ps-4 border-left-dashed">
                          <div class="timeline-event ps-0 pb-0">
                            <h6 class="mb-0"><a href="{{route('admin.invoices.edit', [$ec_30])}}">{{runtimeInvIdFormat($ec_30->id)}}</a></h6>
                            <p class="text-muted mb-0 text-nowrap"><b>Value: </b>{{cMoney($ec_30->total, $ec_30->currency, true)}}, <b>Expiring At: </b>{{formatDateTime($ec_30->due_date)}}</p>
                          </div>
                        </li>
                        @empty
                        @endforelse
                      </ul>
                    </div>

                    <div class="tab-pane fade" id="navs-ex-60-days" role="tabpanel">
                      <ul class="timeline timeline-advance timeline-advance mb-2 pb-1">
                        @forelse ($expiringInvoicesList->whereBetween('due_date', [now()->addDays(30), now()->addDays(60)])  as $ec_60)
                        <li class="timeline-item ps-4 border-left-dashed">
                          <div class="timeline-event ps-0 pb-0">
                            <h6 class="mb-0"><a href="{{route('admin.invoices.edit', [$ec_60])}}">{{runtimeInvIdFormat($ec_60->id)}}</a></h6>
                            <p class="text-muted mb-0 text-nowrap"><b>Value: </b>{{cMoney($ec_60->total, $ec_60->currency, true)}}, <b>Expiring At: </b>{{formatDateTime($ec_60->due_date)}}</p>
                          </div>
                        </li>
                        @empty
                        @endforelse
                      </ul>
                    </div>
                    <div class="tab-pane fade" id="navs-ex-90-days" role="tabpanel">
                      <ul class="timeline timeline-advance timeline-advance mb-2 pb-1">
                        @forelse ($expiringInvoicesList->whereBetween('due_date', [now()->addDays(60), now()->addDays(90)])  as $ec_90)
                        <li class="timeline-item ps-4 border-left-dashed">
                          <div class="timeline-event ps-0 pb-0">
                            <h6 class="mb-0"><a href="{{route('admin.invoices.edit', [$ec_90])}}">{{runtimeInvIdFormat($ec_90->id)}}</a></h6>
                            <p class="text-muted mb-0 text-nowrap"><b>Value: </b>{{cMoney($ec_90->total, $ec_90->currency, true)}}, <b>Expiring At: </b>{{formatDateTime($ec_90->due_date)}}</p>
                          </div>
                        </li>
                        @empty
                        @endforelse
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      <!--/ Sales by Countries tabs -->
    </div>


    <div class="row">
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Invoices By Status</h5>
            </div>
          </div>
          <div class="card-body">
            <div id="invoicesByStatus"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Invoices By Distribution</h5>
            </div>
          </div>
          <div class="card-body">
            <div id="invoicesByDistribution"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
