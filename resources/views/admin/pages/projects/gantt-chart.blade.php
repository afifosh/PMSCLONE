@extends('admin/layouts/layoutMaster')

@section('title', 'Task Templates')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-task-board.css')}}" />
{{-- <link rel="stylesheet" href="https://zehntech.github.io/zt-gantt/style.css"/> --}}
{{-- <link rel="stylesheet" href="https://zehntech.github.io/zt-gantt/gantt.css"/> --}}
<link rel="stylesheet" href="{{asset('assets/libs/zt-gantt/gantt.css')}}" />
<style>
  #ZT-Gantt {
  /* width: 100vw; */
  height: calc(100vh - 100px);
}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
{{-- <script type="text/javascript" src="https://zehntech.github.io/zt-gantt/gantt.js"></script> --}}
<script src="{{asset('assets/libs/zt-gantt/gantt.js')}}"></script>
<script src="{{asset('assets/libs/zt-gantt/gantt.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/cards-actions.js')}}></script>
<script>
  function formateDate(dateString){
    const dateObj = new Date(dateString);
    return `${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getDate().toString().padStart(2, '0')}-${dateObj.getFullYear()}`;
  }

  function formateData(rawData){
    let data = [];
    rawData.forEach((project) => {
      // data.push({
      //   id: 'Project:' + project.id,
      //   type: "Project",
      //   text: project.name,
      //   parent: 0,
      //   progress: 0
      // });
      project.contracts.forEach((contract) => {
        // update min and max dates
        if(minDate == null || new Date(contract.start_date) < new Date(minDate)){
          minDate = contract.start_date;
        }
        if(maxDate == null || new Date(contract.end_date) > new Date(maxDate)){
          maxDate = contract.end_date;
        }
        // end update min and max dates
        let contractData = {
          id: 'Contract:' + contract.id,
          text: contract.subject,
          projectName: project.name,
          // parent: 'Project:' + project.id,
          parent: 0,
          type: "Contract",
          status: contract.status,
          progress: 0,
          start_date: formateDate(contract.start_date),
          end_date: formateDate(contract.end_date),
        };
        data.push(contractData);
        contract.phases.forEach((phase) => {
          // update min and max dates
          if(new Date(phase.start_date) < new Date(minDate)){
            minDate = phase.start_date;
          }
          if(new Date(phase.due_date) > new Date(maxDate)){
            maxDate = phase.due_date;
          }
          // end update min and max dates
          let taskData = {
            id: 'Phase:' + phase.id,
            text: phase.name,
            parent: 'Contract:' + contract.id,
            contractName: contract.subject,
            projectName: project.name,
            status: phase.status,
            progress: 0,
            type: "Phase",
            start_date: formateDate(phase.start_date),
            end_date: formateDate(phase.due_date),
          };
          data.push(taskData);
        });
      });
    });

    return data;
  }
  var minDate = null;
  var maxDate = null;
  let projects = {!!json_encode($ganttProjects)!!};
  let data = formateData(projects);

  let element = document.getElementById("ZT-Gantt");
  let ZT_Gantt = new ztGantt(element);

  ZT_Gantt.options.columns = [
    {
      name: "text",
      width: 400,
      min_width: 300,
      max_width: 500,
      tree: true,
      label: "Contract",
      resize: true,
      template: (task) => {
        return `<span>${task.text}</span>`;
      },
    }
  ];

  ZT_Gantt.options.date_format = "%m-%d-%Y";
  ZT_Gantt.options.localLang = "en";
  ZT_Gantt.options.data = data;
  ZT_Gantt.options.collapse = false;
  ZT_Gantt.options.weekends = ["Sat", "Sun"];
  ZT_Gantt.options.fullWeek = true;
  ZT_Gantt.options.todayMarker = true;
  ZT_Gantt.options.addLinks = (task)=>{
    if(task.parent === 0){
      return false;
    }
    return true;
  };

  ZT_Gantt.options.exportApi = "https://zt-gantt.zehntech.net/";
  ZT_Gantt.options.taskColor = true;
  ZT_Gantt.options.taskOpacity = 0.7;
  // ZT_Gantt.options.links = [
  //   { id: 1, source: 2, target: 23, type: 1 },
  //   { id: 2, source: 3, target: 6, type: 2 },
  //   { id: 3, source: 4, target: 23, type: 3 },
  //   { id: 4, source: 12, target: 15 },
  // ];
  ZT_Gantt.options.weekStart = 1; // set the start of the week
  ZT_Gantt.options.sidebarWidth = 300;
  ZT_Gantt.options.scales = [
    {
      unit: "week",
      step: 1,
      format: (t) => {
        const { startDate: a, endDate: n, weekNum: i } = weekStartAndEnd(t);
        return ` ${ZT_Gantt.formatDateToString(
          "%j %M",
          a
        )} - ${ZT_Gantt.formatDateToString(
          "%j %M",
          n
        )}, ${a.getFullYear()}`;
      },
    },
    { unit: "day", step: 1, format: "%d %D" },
  ];

  ZT_Gantt.options.zoomLevel = "week";

  // zoom config
  ZT_Gantt.options.zoomConfig = {
    levels: [
      {
        name: "hour",
        scale_height: 27,
        min_col_width: 550,
        scales: [
          { unit: "day", step: 1, format: "%d %M" },
          { unit: "hour", step: 1, format: "%H" },
        ],
      },
      {
        name: "day",
        scale_height: 27,
        min_col_width: 80,
        scales: [
          { unit: "week", step: 1, format: "%W" },
          { unit: "day", step: 1, format: "%d %M" },
        ],
      },
      {
        name: "week",
        scale_height: 45,
        min_col_width: 50,
        scales: [
          { unit: "month", step: 1, format: "%M" },
          {
            unit: "week",
            step: 1,
            format: (t) => {
              const {
                startDate: a,
                endDate: n,
                weekNum: i,
              } = weekStartAndEnd(t);
              return ` ${ZT_Gantt.formatDateToString(
                "%j %M",
                a
              )} - ${ZT_Gantt.formatDateToString(
                "%j %M",
                n
              )}, ${a.getFullYear()}`;
            },
          },
        ],
      },
      {
        name: "month",
        scale_height: 30,
        min_col_width: 120,
        scales: [
          { unit: "year", step: 1, format: "%Y" },
          { unit: "month", step: 1, format: "%M" },
        ],
      },
      {
        name: "quarter",
        scale_height: 25,
        min_col_width: 90,
        scales: [
          { unit: "year", step: 1, format: "%Y" },
          { unit: "quarter", step: 1, format: "Q%q" },
          { unit: "month", format: "%M" },
        ],
      },
      {
        name: "year",
        scale_height: 30,
        min_col_width: 30,
        scales: [
          { unit: "year", step: 3, format: new Date().getFullYear() - 1 + ' - ' + ( new Date().getFullYear() + 1)},
          { unit: "year", step: 1, format: "%Y" },
          { unit: "month", format: "%M" },
        ],
      },
    ],
  };

  ZT_Gantt.options.scale_height = 30;
  ZT_Gantt.options.row_height = 24;
  ZT_Gantt.options.minColWidth = 80;
  ZT_Gantt.options.addTaskOnDrag = false;
  ZT_Gantt.options.taskProgress = false;

  function weekStartAndEnd(t) {
    const e = t.getDay();
    let a, n;
    0 === e
      ? ((a = ZT_Gantt.add(t, -6, "day")), (n = t))
      : ((a = ZT_Gantt.add(t, -1 * e + 1, "day")),
        (n = ZT_Gantt.add(t, 7 - e, "day")));
    return {
      startDate: a,
      endDate: n,
      weekNum: ZT_Gantt.formatDateToString("%W", t),
    };
  }

  ZT_Gantt.options.startDate = new Date(minDate).toISOString();;
  ZT_Gantt.options.endDate = new Date(maxDate).toISOString();;

  ZT_Gantt.templates.tooltip_text = function (start, end, task) {
    return `
        <b>Project:</b>${task.projectName}<br/>
        ${task.type == 'Phase' ? `<b>Contract:</b>${task.contractName}<br/>` : ''}
        <b>${task.type}:</b>${task.text}<br/>
        <b>Start date:</b>
        ${ZT_Gantt.formatDateToString("%d-%m-%y", task.start_date)}<br/>
        <b>End date:</b>
        ${ZT_Gantt.formatDateToString("%d-%m-%y", task.end_date)}<br/>
        <b>Status:</b>${task.status}<br/>
        <b>Duration:</b> ${task.duration} ${task.duration > 1 ? "Days" : "Day"}<br/>
        ${Math.ceil((new Date(task.end_date) - new Date()) / (1000 * 60 * 60 * 24)) > 0 ? '<b>Remaining Days:</b>' + Math.ceil((new Date(task.end_date) - new Date()) / (1000 * 60 * 60 * 24)) : ''}
    `;
  };

  ZT_Gantt.templates.taskbar_text = function (start, end, task) {
    return task.type + " : " + task.text;
  };

  ZT_Gantt.templates.grid_folder = (task) => {
    var name = task?.text?.trim().split(" ");
    var firstname = name?.[0];
    var lastname = name?.[1] ? name?.[1] : "";
    var intials =
      firstname?.charAt(0)?.toUpperCase() +
      lastname?.charAt(0)?.toUpperCase();
    return `<div></div>`;
  };

  ZT_Gantt.templates.grid_file = (task) => {
    if (task.parent != 0) {
      // return '';
      var tracker_name = task.hasOwnProperty("tracker")
        ? task.tracker.name
        : " ";
      let issue_id = task.id;
      return `<div class='gantt_file ${tracker_name}'><b  class="link-issue ${tracker_name}">${task.type} :  </b></div>`;
    }
  };

  ZT_Gantt.templates.task_drag = (mode, task) => {
    if (task.parent == 0 || (task.children && task.children.length > 0)) {
      // || task.children
      return false;
    }
    return true;
  };

  // add custom classes
  // ZT_Gantt.templates.grid_header_class = (columns,i) => {
  //   return "my-header-class test"
  // }
  // ZT_Gantt.templates.grid_row_class = (start, end, task) => {
  //   console.log(start, end);
  //   return "my-grid-row-class test"
  // }
  ZT_Gantt.templates.task_class = (start, end, task) => {
    if (task.parent == 0) {
      return "parent-task";
    } else {
      return "child-task";
    }
  };
  // ZT_Gantt.templates.task_row_class = (start, end, task) => {
  //   return "my-task-row-class test"
  // }
  ZT_Gantt.templates.scale_cell_class = (date, scale, scaleIndex) => {
    if (scaleIndex === 1) {
      return "my-scale-class-2";
    } else {
      return "";
    }
  };
  // ZT_Gantt.templates.grid_cell_class = (col, task) => {
  //   return "my-grid-cell-class test"
  // }
  // ZT_Gantt.templates.timeline_cell_class = (task, date) => {
  //   return "my-task-cell-class  Test  "
  // }

  // add custom marker
  ZT_Gantt.addMarker({
    start_date: ZT_Gantt.add(new Date(), 1, "day"), //a Date object that sets the marker's date
    css: "tomorrow", //a CSS class applied to the marker
    text: "Tomorrow", //the marker title
    title: ZT_Gantt.formatDateToString(
      "%d %F %y",
      ZT_Gantt.add(new Date(), 1, "day")
    ), // the marker's tooltip
  });

  // ZT_Gantt.addMarker({
  //   start_date: ZT_Gantt.add(new Date(),-1, "day"), //a Date object that sets the marker's date
  //   css: "yesterday", //a CSS class applied to the marker
  //   text: "Yesterday", //the marker title
  //   title: ZT_Gantt.formatDateToString("%d %F %Y", ZT_Gantt.add(new Date(),-1, "day")), // the marker's tooltip
  // });

  // render gantt
  ZT_Gantt.render(element);

  // console.log(ZT_Gantt);

  // to find task in gantt
  // console.log(ZT_Gantt.getTask(69));

  // custom events
  ZT_Gantt.attachEvent("onTaskDblClick", (event) => {
    // console.log("onTaskDblClick: ", event);
  });
  let idCount = 0;
  ZT_Gantt.attachEvent("addTaskOnDrag", (event) => {
    // console.log("addTaskOnDrag: ", event.task);
    ZT_Gantt.addTask({
      id: "Added" + idCount,
      start_date: new Date(event.task.startDate),
      end_date: new Date(event.task.endDate),
      parent: event.task.parent,
      text: "Task Added",
    });
    ZT_Gantt.render();
    idCount += 1;
  });
  ZT_Gantt.attachEvent("onLinkDblClick", (event) => {
    // console.log("onLinkDblClick: ", event);
  });
  ZT_Gantt.attachEvent("onBeforeLinkAdd", (event) => {
    // console.log("onBeforeLinkAdd: ", event);
  });
  ZT_Gantt.attachEvent("onLinkAdd", (event) => {
    // console.log("onLinkAdd: ", event);
  });
  ZT_Gantt.attachEvent("onDeleteLink", (event) => {
    // console.log("onDeleteLink: ", event);
  });
  ZT_Gantt.attachEvent("onBeforeTaskDrag", (event) => {
    // console.log("onBeforeTaskDrag: ", event);
    if (event.task.children.length !== 0) {
      return false;
    } else {
      return true;
    }
  });
  ZT_Gantt.attachEvent("onTaskDrag", (event) => {
    // console.log("onTaskDrag: ", event);
  });
  ZT_Gantt.attachEvent("onAfterTaskDrag", (event) => {
    // console.log("onAfterTaskDrag: ", event);
  });
  ZT_Gantt.attachEvent("onBeforeTaskDrop", (event) => {
    // console.log("onBeforeTaskDrop: ", event);
    if (event.parentTask.id == 12) {
      return false;
    }
  });
  ZT_Gantt.attachEvent("onTaskDelete", (event) => {
    // console.log("onTaskDelete: ", event);
  });
  ZT_Gantt.attachEvent("onAfterTaskUpdate", (event) => {
    // console.log("onAfterTaskUpdate: ", event);
  });
  ZT_Gantt.attachEvent("onCellClick", (event) => {
    // console.log("onCellClick: ", event);
  });
  ZT_Gantt.attachEvent("onExpand", (event) => {
    // console.log("onExpand: ", event);
  });
  ZT_Gantt.attachEvent("onCollapse", (event) => {
    // console.log("onCollapse: ", event);
  });
  // ZT_Gantt.attachEvent("onScroll", (event) => {
  //   console.log("onScroll: ", event);
  // });
  // ZT_Gantt.attachEvent("onResize", (event) => {
  //   console.log("onResize: ", event);
  // });
  ZT_Gantt.attachEvent("onAfterProgressDrag", (event) => {
    // console.log("onAfterProgressDrag: ", event);
  });
  ZT_Gantt.attachEvent("onBeforeProgressDrag", (event) => {
    // console.log("onBeforeProgressDrag: ", event);
    // if(event.task.parent === 0){
    //   return false;
    // }else{
    //   return true;
    // }
  });
  ZT_Gantt.attachEvent("onAutoScheduling", (event) => {
    // console.log("onAutoScheduling: ", event);
  });
  ZT_Gantt.attachEvent("onColorChange", (event) => {
    // console.log("onColorChange: ", event);
  });

  let fullscreen = false;
  function changeScreen() {
    if (fullscreen === false) {
      ZT_Gantt.requestFullScreen();
      // ZT_Gantt.openTask(3);
    } else {
      ZT_Gantt.exitFullScreen();
    }
  }

  function changeZoom(e) {
    ZT_Gantt.options.zoomLevel = e.target.value;
    if (e.target.value === "month" || e.target.value === "quarter") {
      ZT_Gantt.options.startDate = "2023-01-01T11:46:17.775Z";
      ZT_Gantt.options.endDate = "2023-12-31T11:46:17.775Z";
    } else if (e.target.value === "year") {
      ZT_Gantt.options.startDate = "2022-01-01T11:46:17.775Z";
      ZT_Gantt.options.endDate = "2024-12-31T11:46:17.775Z";
    } else {
      ZT_Gantt.options.startDate = "2023-06-01T11:46:17.775Z";
      ZT_Gantt.options.endDate = "2023-06-30T11:46:17.775Z";
    }
    ZT_Gantt.zoomInit();
  }

  function changeLang(e) {
    ZT_Gantt.setLocalLang(e.target.value);
  }

  function changeCollapse(e) {
    if (e.target.checked === true) {
      ZT_Gantt.collapseAll();
    } else {
      ZT_Gantt.expandAll();
    }
  }

  function changeSidebar(e) {
    if (e.target.checked === true) {
      $('#zt-gantt-grid-left-data').show()
      $('#zt-gantt-left-layout-resizer-wrap').show()
    } else {
      $('#zt-gantt-grid-left-data').hide()
      $('#zt-gantt-left-layout-resizer-wrap').hide()
    }
  }

  function changeToday(e) {
    if (event.target.checked === true) {
      ZT_Gantt.addTodayFlag();
    } else {
      ZT_Gantt.removeTodayFlag();
    }
  }

  function exportChange(e) {
    let stylesheet = ['https://zehntech.github.io/zt-gantt/gantt.css','https://zehntech.github.io/zt-gantt/style.css']
    if (e.target.value === "png") {
      ZT_Gantt.exportToPNG("ztGanttChart", stylesheet);
    } else if (e.target.value === "pdf") {
      ZT_Gantt.exportToPDF("ztGanttChart", stylesheet);
    } else {
      ZT_Gantt.exportToExcel("ztGanttChart");
    }
    e.target.value = "";
  }

  function autoScheduling() {
    ZT_Gantt.autoScheduling();
  }
  function addTask() {
    ZT_Gantt.addTask({
      id: 5354653546,
      tracker_id: 4,
      project_id: 86,
      subject:
        "Workflow - In the Workflow view, JOC reacts slow when handling large workflows or multiple smaller workflows in the same folder.",
      description:
        "requirements-\r\nwhen in the WORKFLOW view a larger workflow (several hundred jobs) or multiple smaller workflows in the same folder are completely expanded then JOC reacts rather slowly.\r\nThis affects actions like scrolling, opening instruction and order menus and executing items of these menus.",
      due_date: "2023-05-17",
      category_id: null,
      status_id: 2,
      assigned_to_id: 308,
      priority_id: 2,
      fixed_version_id: null,
      author_id: 308,
      lock_version: 3,
      created_on: "2023-05-18T05:03:17.000Z",
      updated_on: "2023-05-18T05:03:25.000Z",
      start_date: "2023-05-17",
      done_ratio: 70,
      estimated_hours: 8.5,
      parent: 12,
      parent_id: null,
      root_id: 53546,
      lft: 1,
      rgt: 2,
      is_private: false,
      closed_on: null,
      tag_list: [],
    });
    // setTimeout(()=>{

    //   ZT_Gantt.openTask(280);
    // },0)
    // ZT_Gantt.parse(data);
    ZT_Gantt.render();
    // ZT_Gantt.deleteLink(1);
  }

  // get the position of the cell
  // console.log(ZT_Gantt.posFromDate(new Date()));

  // iterate over each task
  // ZT_Gantt.eachTask((task)=>{
  //   console.log(task._id,"task _id ?????????????????????????????????");
  // })
  let cssStyle;
  let root = document.querySelector(":root");
  function changeTheme(event) {
    if (event.target.checked) {
      cssStyle = document.createElement("link");
      cssStyle.setAttribute("rel", "stylesheet");
      cssStyle.setAttribute("href", "./theme/dark.css");
      document.getElementsByTagName("head")[0].append(cssStyle);

      root.style.setProperty("--bg-color", "#333332");
      root.style.setProperty("--text-color", "#fff");
      root.style.setProperty("--text-secondary-color", "#fff");
      root.style.setProperty("--index-primary-color", "#1395BE");
      root.style.setProperty("--index-primary-hover-color", "#0E7595");
    } else {
      cssStyle.remove();
      root.style.setProperty("--bg-color", "#fff");
      root.style.setProperty("--text-color", "#000");
      root.style.setProperty("--text-secondary-color", "#fff");
      root.style.setProperty("--index-primary-color", "#4ca0fff2");
      root.style.setProperty("--index-primary-hover-color", "#3585e0f2");
    }
  }

  let getScale = () => {
    console.log(ZT_Gantt.getScale());
  };

  function searchTask(e) {
    let isFilter = e.target.value.trim() !== "";
    let parentIds = [];
    ZT_Gantt.filterTask((task) => {
      return task.text.toLowerCase().includes(e.target.value.toLowerCase()) && parentIds.push(task.parent);
    }, isFilter);
    ZT_Gantt.filterTask((task) => {
      return parentIds.includes(task.id) || task.text.toLowerCase().includes(e.target.value.toLowerCase());
    }, isFilter);
  }
  function addCol() {
    ZT_Gantt.options.columns.push({
      name: "progress",
      width: 245,
      min_width: 80,
      max_width: 300,
      tree: false,
      label: "Progress",
      resize: true,
      align: "center",
      template: (task) => {
        return `<span>${task.progress || 0}</span>`;
      },
    });
    ZT_Gantt.render();
  }

  function removeCol() {
    ZT_Gantt.options.columns.splice(ZT_Gantt.options.columns.length - 1, 1);
    ZT_Gantt.render();
  }

  $(document).on('change', '.gantt_filter', function (e) {
    e.preventDefault();
    applyFilters();
  });
  function applyFilters(){
    $.ajax({
      type: "get",
      url: route('admin.projects.gantt-chart.index'),
      data: {
        projects: $('[name="projects[]"]').val(),
        status: $('[name="status"]').val(),
        companies: $('[name="companies[]"]').val()
      },
      success: function (ids) {
        console.log(ids);
        ZT_Gantt.filterTask((task) => {
          console.log(task.id, ids.includes(task.id));
          return ids.includes(task.id);
        }, true);
      }
    });
  }
</script>
@endsection

@section('content')
@includeWhen(isset($project),'admin.pages.projects.navbar', ['tab' => 'gantt-chart'])
<div class="col-12 mt-3">
  <div class="card card-action mb-4">
    <div class="card-header">
      <h5 class="card-action-title">Contracts</h5>
      <div class="card-action-element">
        <ul class="list-inline mb-0">
          <li class="list-inline-item">
            <a href="javascript:void(0);" onclick="setTimeout(function(){ZT_Gantt.render()}, 300)" class="card-expand"><i class="tf-icons ti ti-arrows-maximize ti-sm"></i></a>
          </li>
        </ul>
      </div>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
          @if (!isset($project))
            <div class="col-md-3">
              {!! Form::label('projects', 'Projects') !!}
              {!! Form::select('projects[]', $projects, null, ['class' => 'form-select gantt_filter', 'data-placeholder' => 'Projects']) !!}
            </div>
            <div class="col-md-3">
              {!! Form::label('project Status', 'Project Status') !!}
              {!! Form::select('status', [null => 'All'] + $statuses, null, ['class' => 'form-select gantt_filter', 'data-placeholder' => 'Status']) !!}
            </div>
            <div class="col-md-3">
              {!! Form::label('companies[]', 'Company') !!}
              {!! Form::select('companies[]', $companies, null, ['class' => 'form-select gantt_filter', 'data-placeholder' => 'Company']) !!}
            </div>
          @endif
          <div class="col-md-3">
            {!! Form::label('search', 'Search') !!}
            {!! Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'Search', 'onkeyup' => 'searchTask(event)']) !!}
          </div>

          <div class="{{!isset($project) ? 'mt-3' : ''}} col-3">
            <label>Zoom To</label>
            <select class="form-select" onchange="changeZoom(event)">
              <option value="year">Years</option>
              <option value="quarter">Quarters</option>
              <option value="month">Months</option>
              <option value="week">Weeks</option>
              <option value="day" selected="day">Days</option>
              <option value="hour">Hour</option>
            </select>
          </div>
          <div class="col-md-2 d-flex justify-content-between pt-4 me-5">
            <label class="switch">
              <label for="collapse-zt" class="me-2">Collapse</label>
              <input id="collapse-zt" type="checkbox" class="switch-input" onchange="changeCollapse(event)">
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
            </label>
            <label class="switch">
              <label for="sidebar-zt" class="me-2">SideBar</label>
              <input id="sidebar-zt" type="checkbox" class="switch-input" onchange="changeSidebar(event)" checked>
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
            </label>
          </div>
        </div>
      <div class="mt-4" id="ZT-Gantt"></div>
    </div>
  </div>
</div>
@endsection
