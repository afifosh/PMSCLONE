@extends('admin/layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<style>
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  }

  #chartdiv {
    width: 100%;
    height: 1000px;
  }
</style>
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
<style>
  .bullet {
    width: 15px;
    height: 10px;
    border-radius: 20%;
    display: inline-block;
  }
</style>
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script>
  const contractEventsSummary = {!! json_encode($summary) !!}
  $(document).ready(function () {
    if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }
    const chartColors = {
    donut: {
      series1: config.colors.warning,
      series2: config.colors.danger,
      series3: config.colors.success,
      series4: config.colors.secondary
    }
  };
    // Generated Leads Chart
  // --------------------------------------------------------------------
  const eventsSummaryChartEl = document.querySelector('#eventsSummaryChart'),
    eventsSummaryChartConfig = {
      chart: {
        height: 147,
        width: 130,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: ['Paused', 'Terminated'],
      series: [
                parseInt($('.paused-event-count').text()),
                parseInt($('.terminated-event-count').text())
              ],
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series2,
        chartColors.donut.series3,
        chartColors.donut.series4
      ],
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: false,
        formatter: function (val, opt) {
          return parseInt(val) + '%';
        }
      },
      legend: {
        show: false
      },
      tooltip: {
        theme: false
      },
      grid: {
        padding: {
          top: 15,
          right: -20,
          left: -20
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              value: {
                fontSize: '1.375rem',
                fontFamily: 'Public Sans',
                color: headingColor,
                fontWeight: 500,
                offsetY: -15,
                formatter: function (val) {
                  return parseInt(val) + '%';
                }
              },
              name: {
                offsetY: 20,
                fontFamily: 'Public Sans'
              },
              total: {
                show: true,
                showAlways: true,
                color: config.colors.success,
                fontSize: '.8125rem',
                label: 'Total',
                fontFamily: 'Public Sans',
                formatter: function (w) {
                  return parseInt($('.total-summary-event-count').text()) ;
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 1025,
          options: {
            chart: {
              height: 172,
              width: 160
            }
          }
        },
        {
          breakpoint: 769,
          options: {
            chart: {
              height: 178
            }
          }
        },
        {
          breakpoint: 426,
          options: {
            chart: {
              height: 147
            }
          }
        }
      ]
    };
  if (typeof eventsSummaryChartEl !== undefined && eventsSummaryChartEl !== null) {
    const eventsSummaryChart = new ApexCharts(eventsSummaryChartEl, eventsSummaryChartConfig);
    eventsSummaryChart.render();
  }
  });
</script>
@endsection

@section('content')
@include('admin.pages.contracts.header', ['tab' => 'overview'])
<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-12 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase">About</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-user"></i><span class="fw-bold mx-2">Subject:</span> <span>{{$contract->subject}}</span></li>
          @if ($contract->assignable_id != null && $contract->assignable_type != null)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Assigned To ({{explode('\\', $contract->assignable_type)[2]}}):</span> <span>{{$contract->assignable->name ?? $contract->assignable->first_name .' '. $contract->assignable->last_name}}</span></li>
          @endif
          @if ($contract->project_id)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-user"></i><span class="fw-bold mx-2">Project:</span> <span>{{$contract->project->name ?? ''}}</span></li>
          @endif
          <li class="d-flex align-items-center mb-3"><i class="ti ti-check"></i><span class="fw-bold mx-2">Status:</span> <span>{{$contract->status}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-crown"></i><span class="fw-bold mx-2">Value:</span> <span>{{$contract->printable_value}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Start Date:</span> <span>{{formatDateTime($contract->start_date)}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">End Date:</span> <span>{{formatDateTime($contract->end_date)}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Type:</span> <span>{{$contract->type->name ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Refrence ID:</span> <span>{{$contract->refrence_id}}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-flag"></i><span class="fw-bold mx-2">Description:</span> <span>{{$contract->description}}</span></li>
        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Activity Timeline -->
    <div class="row">
      <div class="col-xl-6 mb-4 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="card-title mb-auto">
              <h5 class="mb-1 text-nowrap">Events summary</h5>
            </div>
            <div class="d-flex justify-content-start">
              <div class="d-flex me-5">
                <div id="eventsSummaryChart"></div>
              </div>
              <div class="mt-2 me-5">
                <div class="d-flex fs-6 justify-content-between fw-semibold mb-3">
                  <div class="bullet bg-warning mt-1 me-1"></div>
                  <div class="text-muted me-5">Paused</div>
                  <div class="ms-auto fw-bold paused-event-count">{{$summary->where('event_type', 'Paused')->sum('total')}}</div>
                </div>
                <div class="d-flex fs-6 justify-content-between fw-semibold mb-3">
                  <div class="bullet bg-secondary mt-1 me-1"></div>
                  <div class="text-muted me-5">Terminated</div>
                  <div class="ms-auto fw-bold terminated-event-count">{{$summary->where('event_type', 'Terminated')->sum('total')}}</div>
                  <div class="d-none total-summary-event-count">{{$summary->whereIn('event_type', ['Paused', 'Terminated'])->sum('total')}}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title mb-auto">
            <h5 class="mb-1 text-nowrap">Funds Flow</h5>
          </div>
          <div id="chartdiv"></div>
        </div>
      </div>
    </div>
    <!-- Projects table -->
    {{-- <div class="card mb-4">
      <div class="card-datatable table-responsive">
        {{$dataTable->table()}}
      </div>
    </div> --}}
    <!--/ Projects table -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
@push('scripts')
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script>
// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv", am4charts.SankeyDiagram);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
chart.data = {!! json_encode($sankey_funds_data) !!};

let hoverState = chart.links.template.states.create("hover");
hoverState.properties.fillOpacity = 0.6;

chart.dataFields.fromName = "from";
chart.dataFields.toName = "to";
chart.dataFields.value = "value";

chart.links.template.propertyFields.id = "id";
chart.links.template.colorMode = "solid";
chart.links.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
chart.links.template.fillOpacity = 0.1;
chart.links.template.tooltipText = "";

// highlight all links with the same id beginning
chart.links.template.events.on("over", function(event){
  let link = event.target;
  let id = link.id.split("-")[0];

  chart.links.each(function(link){
    if(link.id.indexOf(id) != -1){
      link.isHover = true;
    }
  })
})

chart.links.template.events.on("out", function(event){
  chart.links.each(function(link){
    link.isHover = false;
  })
})

// for right-most label to fit
chart.paddingRight = 200;

// make nodes draggable
var nodeTemplate = chart.nodes.template;
nodeTemplate.width = 20;
nodeTemplate.cursorOverStyle = am4core.MouseCursorStyle.pointer


nodeTemplate.events.on("hit", function(ev) {
  // Assemble nodes that need to be stay open
  var focusNodes = [ev.target];
  collectFocusNodes(focusNodes, ev.target);

  // Toggle all nodes off except one that belongs to trace
  chart.dataItems.each(function(item) {
    if (focusNodes.indexOf(item.fromNode) !== -1) {
      item.fromNode.show();
      item.toNode.show();
    }
    else {
      item.fromNode.hide();
      item.toNode.hide();
    }
  })
});

function collectFocusNodes(focusNodes, target) {
  var items = target.incomingDataItems;
  target.incomingDataItems.each(function(item) {
    focusNodes.unshift(item.fromNode);
    collectFocusNodes(focusNodes, item.fromNode);
  })
}
</script>
@endpush
