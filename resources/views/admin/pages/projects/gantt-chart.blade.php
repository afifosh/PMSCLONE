@extends('admin/layouts/layoutMaster')

@section('title', 'Task Templates')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-task-board.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.5.0/dist/frappe-gantt.css" />
<style>
  .pro-bar .bar {
    fill: tomato;
  }
  .con-bar .bar {
    fill: rgba(221, 49, 18, 0.514);
  }
  .pha-bar .bar {
    fill: rgba(139, 141, 11, 0.61);
  }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.5.0/dist/frappe-gantt.min.js"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  $(document).ready(function () {
    window.tasks = {!! json_encode($tasks) !!};
    window.gantt = new Gantt("#gantt", tasks,{
        on_click: function (task) {
            toggleBars(task);
        },
        view_mode: 'Week',
        language: 'en',
        custom_popup_html: null
    });
  });
  $(document).on('click', '[data-view]', function (e) {
    e.preventDefault();
    var mode = $(this).attr('data-view');
    $('[data-view]').removeClass('selected');
    $(this).addClass('selected');
    gantt.change_view_mode(mode);
  });

  // collapse all tasks

  function toggleBars(task) {

  }
</script>
@endsection

@section('content')
@includeWhen(isset($project),'admin.pages.projects.navbar', ['tab' => 'gantt-chart'])
<div class="mt-3 card">
  <div class="d-flex justify-content-between">
    <div>
      <button class="btn btn-sm btn-primary" type="button" data-view="Quarter Day">Quarter Day</button>
      <button class="btn btn-sm btn-primary" type="button" data-view="Half Day">Half Day</button>
      <button class="btn btn-sm btn-primary" type="button" data-view="Day">Day</button>
      <button class="btn btn-sm btn-primary" type="button" data-view="Week">Week</button>
      <button class="btn btn-sm btn-primary" type="button" data-view="Month" class="selected">Month</button>
    </div>
    <div>
      <button class="btn btn-sm btn-primary" type="button" onclick="collapseAll();">Collapse all</button>
      <button class="btn btn-sm btn-primary" type="button" onclick="expandBars();">Expand all</button>
    </div>
  </div>
  <svg id="gantt"></svg>
</div>
@endsection
