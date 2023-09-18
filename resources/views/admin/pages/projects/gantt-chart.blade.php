@extends('admin/layouts/layoutMaster')

@section('title', 'Contracts Gantt Chart')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-task-board.css')}}" />
<link rel="stylesheet" href="{{asset('assets/libs/dhtmlxgantt/dhtmlxgantt.css')}}" type="text/css">
<style>
.gantt_container, .gantt_tooltip {
    background-color: #fff;
    font-family: Arial;
    font-size: 15px !important;
  }
.gantt_tooltip{
 z-index: 999999999 !important;
}
/* highlight column on click */
.highlighted-column {
  background-color: #fff3a1;
}

.gantt_task_scale .gantt_scale_cell {
  cursor: default;
}

.gantt_task_scale .gantt_scale_cell.highlighted-column {
  color: #454545;
  font-weight: bold;
}
/* End : highlight column on click */
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/libs/dhtmlxgantt/dhtmlxgantt.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/cards-actions.js')}}></script>
<script>
  function formateDate(dateString){
    const dateObj = new Date(dateString);
    return `${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getDate().toString().padStart(2, '0')}-${dateObj.getFullYear()}`;
  }

  function calculateDateDifference(date1, date2) {
    // Create Date objects from the input strings
    const d1 = new Date(date1);
    const d2 = new Date(date2);

    // Calculate the difference in milliseconds
    const timeDifference = d2 - d1;

    // Convert milliseconds to days
    const daysDifference = timeDifference / (1000 * 60 * 60 * 24);

    return Math.abs(daysDifference); // Absolute value in case date2 is before date1
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
      //
     //   {"id":1, "text":"Project #2", "start_date":"01-04-2018", "duration":"18", "progress": 0.4, "open": true, color:"#CD545B"},
      rawData.forEach((contract) => {
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
          text: `${contract.subject}`,
          projectName: contract.project?.name,
          assignableType: contract.assignable_id? (contract.assignable_type ? contract.assignable_type.split('\\')[2] : null) : null,
          assignable: contract.assignable_id ? (contract.assignable?.name ?? contract.assignable?.first_name + ' ' + contract.assignable?.last_name) : null,
          // parent: 'Project:' + project.id,
          type: "Contract",
          duration: contract.start_date && contract.end_date ? calculateDateDifference(contract.start_date, contract.end_date) : null,
          status: contract.status,
          progress: calculateProgressPercentage(contract.start_date, contract.end_date),
          start_date: contract.start_date ? new Date(contract.start_date) : '',
          unscheduled:true && contract.start_date == null,
          hasEndDate: true && contract.end_date != null,
          type: contract.end_date == null ? 'milestone' : 'task',
          open: true,
          rollup: true,
          color:"#CD545B",
          remaining_days: contract.end_date ? Math.ceil((new Date(contract.end_date) - new Date()) / (1000 * 60 * 60 * 24)) : 0,
        };
        data.push(contractData);
        // {"id":2, "text":"Task #1", "start_date":"02-04-2018", "duration":"8", "parent":"1", "progress":0.5, "open": true},
        contract.milestones.forEach((phase) => {
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
            projectName: contract.project?.name,
            assignableType: contract.assignable_type ? contract.assignable_type.split('\\')[2] : null,
            assignable: contract.assignable?.name ?? contract.assignable?.first_name + ' ' + contract.assignable?.last_name,
            status: phase.status,
            // calculate from start date and end date and current date
            progress: calculateProgressPercentage(phase.start_date, phase.due_date),
            type: "Phase",
            start_date: new Date(phase.start_date),
            duration: calculateDateDifference(phase.start_date, phase.due_date),
            hasEndDate: true,
            // end_date: formateDate(phase.due_date),
            open: true,
            remaining_days: phase.due_date ? Math.ceil((new Date(phase.due_date) - new Date()) / (1000 * 60 * 60 * 24)) : 0,
          };
          data.push(taskData);
        });
      });
    });

    return data;
  }

  function calculateProgressPercentage(start_date, end_date) {
    start_date = new Date(start_date);
    end_date = new Date(end_date);
    const current_date = new Date();

    // Convert dates to milliseconds
    const start_time = start_date.getTime();
    const end_time = end_date.getTime();
    const current_time = current_date.getTime();

    // Calculate total duration and elapsed duration in milliseconds
    const total_duration = end_time - start_time;
    const elapsed_duration = current_time - start_time;

    // Calculate progress percentage
    const progress_percentage = (elapsed_duration / total_duration) * 100;

    return Math.min(100, Math.max(0, progress_percentage)).toFixed(2); // Ensure the percentage is within 0 to 100 range
  }
  var minDate = null;
  var maxDate = null;
  let projects = {!!json_encode($ganttProjects)!!};
  let data = formateData(projects);

  $(document).on('change keyup', '.gantt_filter', function (e) {
    e.preventDefault();
    applyFilters();
  });
  function applyFilters(){
    $.ajax({
      type: "get",
      url: route('admin.projects.gantt-chart.index'),
      data: {
        projects: $('[name="projects[]"]').val(),
        filter_status: $('[name="filter_status"]').val(),
        companies: $('[name="companies[]"]').val(),
        search_q: $('[name="search_task"]').val(),
        contract_type: $('[name="contract_type"]').val(),
        contract_client: $('[name="contract_client"]').val(),
        programs: $('[name="programs"]').val(),
      },
      success: function (ganttProjects) {
        projects = ganttProjects;
        data = formateData(projects);
        remveAllTasks();
        gantt.parse({
          "data": data,
        });
      }
    });
  }

  function remveAllTasks(){
    if(gantt.getTaskCount() == 0)
      return;
    else{
      try{
        gantt.eachTask(function(task) {
              gantt.deleteTask(task.id);
        });
      }catch(e){
      }
      return remveAllTasks();
    }
  }


  // new gantt

  gantt.attachEvent("onBeforeTaskUpdate", function(id,item){
    //any custom logic here
    zoomToFit();
  });
  // ****************************highlight column on click***************************
  var selected_column = null;

	gantt.attachEvent("onScaleClick", function (e, date) {
		selected_column = date;
		var pos = gantt.getScrollState();
		gantt.render();
		gantt.scrollTo(pos.x, pos.y);
	});

	function is_selected_column(column_date) {
		if (selected_column && column_date.valueOf() == selected_column.valueOf()) {
			return true;
		}
		return false;
	}

	gantt.templates.scale_cell_class = function (date) {
		if (is_selected_column(date))
			return "highlighted-column";
	};
	gantt.templates.timeline_cell_class = function (item, date) {
		if (is_selected_column(date))
			return "highlighted-column";
	};
  // ****************************End : highlight column on click***************************

  // ****************************zoom to fit***************************
	function toggleMode(toggle) {
		gantt.$zoomToFit = !gantt.$zoomToFit;
		if (gantt.$zoomToFit) {
			toggle.innerHTML = "Set default";
			//Saving previous scale state for future restore
			saveConfig();
			zoomToFit();
		} else {

			toggle.innerHTML = "Zoom to Fit";
			//Restore previous scale state
			restoreConfig();
			gantt.render();
		}
	}

	var cachedSettings = {};

	function saveConfig() {
		var config = gantt.config;
		cachedSettings = {};
		cachedSettings.scales = config.scales;
		cachedSettings.start_date = config.start_date;
		cachedSettings.end_date = config.end_date;
		cachedSettings.scroll_position = gantt.getScrollState();
	}

	function restoreConfig() {
		applyConfig(cachedSettings);
	}

	function applyConfig(config, dates) {

		gantt.config.scales = config.scales;
		var lowest_scale = config.scales.reverse()[0];

		if (dates && dates.start_date && dates.end_date) {
			gantt.config.start_date = gantt.date.add(dates.start_date, -1, lowest_scale.unit);
			gantt.config.end_date = gantt.date.add(gantt.date[lowest_scale.unit + "_start"](dates.end_date), 2, lowest_scale.unit);
		} else {
			gantt.config.start_date = gantt.config.end_date = null;
		}

		// restore the previous scroll position
		if (config.scroll_position) {
			setTimeout(function(){
				gantt.scrollTo(config.scroll_position.x, config.scroll_position.y)
			},4)
		}
	}


	function zoomToFit() {
		var project = gantt.getSubtaskDates(),
			areaWidth = gantt.$task.offsetWidth,
			scaleConfigs = zoomConfig.levels;

		for (var i = 0; i < scaleConfigs.length; i++) {
			var columnCount = getUnitsBetween(project.start_date, project.end_date, scaleConfigs[i].scales[scaleConfigs[i].scales.length-1].unit, scaleConfigs[i].scales[0].step);
			if ((columnCount + 2) * gantt.config.min_column_width <= areaWidth) {
				break;
			}
		}


		if (i == scaleConfigs.length) {
			i--;
		}

		gantt.ext.zoom.setLevel(scaleConfigs[i].name);
		applyConfig(scaleConfigs[i], project);
	}

	// get number of columns in timeline
	function getUnitsBetween(from, to, unit, step) {
		var start = new Date(from),
			end = new Date(to);
		var units = 0;
		while (start.valueOf() < end.valueOf()) {
			units++;
			start = gantt.date.add(start, step, unit);
		}
		return units;
	}

	function zoom_in(){
		gantt.ext.zoom.zoomIn();
		gantt.$zoomToFit = false;
		document.querySelector(".zoom_toggle").innerHTML = "Zoom to Fit";
	}
	function zoom_out(){
		gantt.ext.zoom.zoomOut();
		gantt.$zoomToFit = false;
		document.querySelector(".zoom_toggle").innerHTML = "Zoom to Fit";
	}

  function changeCollapse(elm){
    if(elm.checked){
      gantt.eachTask(function(task) {
        gantt.close(task.id);
      });
    }else{
      gantt.eachTask(function(task) {
        gantt.open(task.id);
      });
    }
  }

  function changeSidebar(elm){
    gantt.config.show_grid = elm.checked;
    gantt.render();
  }

  function toggleChart(elm)
  {
    gantt.config.show_chart = elm.checked;
    gantt.render();
  }

  function changeToday(elm){
    if(elm.checked)
    window.todayMarkerId = gantt.addMarker({
      start_date: new Date(),
      css: "today",
      text: "Today",
      title: "Today: "
    });
    else
    gantt.deleteMarker(window.todayMarkerId);
  }

const zoomConfig = {
    levels: [
        {
            name: "hours",
            scales: [
                { unit: "day", step: 1, format: "%j %M" },
                { unit: "hour", step: 1, format: "%H:%i" },
            ],
            round_dnd_dates: true,
            min_column_width: 30,
            scale_height: 60
        },
        {
            name: "days",
            scales: [
                { unit: "week", step: 1, format: "%W" },
                { unit: "day", step: 1, format: "%j" },
            ],
            round_dnd_dates: true,
            min_column_width: 60,
            scale_height: 60
        },
        {
            name: "weeks",
            scales: [
                { unit: "month", step: 1, format: "%M" },
                {
                    unit: "week", step: 1, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%d %M");
                        const endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 60,
            scale_height: 60
        },
        {
            name: "months",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                { unit: "month", step: 1, format: "%M" }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "quarters",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                {
                    unit: "quarter", step: 1, format: function quarterLabel(date) {
                        const month = date.getMonth();
                        let q_num;

                        if (month >= 9) {
                            q_num = 4;
                        } else if (month >= 6) {
                            q_num = 3;
                        } else if (month >= 3) {
                            q_num = 2;
                        } else {
                            q_num = 1;
                        }

                        return "Q" + q_num;
                    }
                },
                { unit: "month", step: 1, format: "%M" }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "years",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                {
                    unit: "year", step: 5, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 5, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "years",
            scales: [
                {
                    unit: "year", step: 10, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 10, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                },
                {
                    unit: "year", step: 100, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 100, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        }
    ]
}
	gantt.ext.zoom.init(zoomConfig);
gantt.config.readonly = true;
gantt.config.sort = true;
	gantt.ext.zoom.setLevel("weeks");

	gantt.$zoomToFit = false;
// Hide task name on task bars
gantt.templates.task_text = function(start, end, task){
    return ""; // return empty string to hide task name
};
	// gantt.message({text: "Scale the Gantt chart to make the whole project fit the screen", expire: -1});

  gantt.plugins({tooltip: true, marker: true});

  changeToday({checked: true});

	gantt.init("gantt_here");

//   gantt.config.tooltip = {
//   template: "#text# <br> Start: #start_date# <br> Endssss: #end_date# <br> End: #duration#"
// };
gantt.templates.tooltip_text = function(start,end,task){
   var formatDate = gantt.date.date_to_str("%d-%m-%Y"); // Customize the date format here
    return `
        ${task.projectName ? `<b>Project:</b>${task.projectName}<br/>` : ''}
        ${task.assignableType && task.assignable ? `<b>${task.assignableType}:</b>${task.assignable}<br/>` : ''}
        ${task.type == 'Phase' ? `<b>Contract:</b>${task.contractName}<br/>` : ''}
        <b>${task.type}:</b>${task.text}<br/>
        ${!task.unscheduled ? `<b>Start date:</b>${formatDate(task.start_date)}<br/> ` : ''}
        ${!task.unscheduled && task.hasEndDate ? `<b>End date:</b>${formatDate(task.end_date)}<br/>` : ''}
        <b>Status:</b>${task.status}<br/>
        ${!task.unscheduled && task.hasEndDate ? `<b>Duration:</b> ${task.duration} ${task.duration > 1 ? "Days" : "Day"}<br/>` : ''}
        ${task.remaining_days > 0 ? '<b>Remaining Days:</b>' + task.remaining_days : ''}
    `;
};

gantt.config.columns = [
    {name:"text", resize:true, label:"Contracts", width:"*", tree: true  },
    {name:"start_date", resize:true, label:"Start Date", align: "center" },
    {name:"end_date", resize:true, label:"End Date", align: "center", template: function($task){
      return $task.hasEndDate ? $task.end_date : '-';
    }},
    {name:"duration", resize:true, label:"Duration", align: "center" , template: function($task){
      return $task.hasEndDate ? $task.duration : '-';
    }},
    {name:"remaining_days", resize:true, label:"Days Left", align: "center" , template: function($task){
      return $task.remaining_days > 0 ? $task.remaining_days : 0;
    }},
    {
        name:"progress", resize:true,
        label:"Progress",
        align: "center",
        template: function(task) {
            return task.hasEndDate ? (task.progress) + '%' : '-';  // Convert the progress to percentage
        }
    }
];
gantt.parse({
	"data": data,
});

function changeZoom(e){
    let scaleValue = e.target.value;
    gantt.ext.zoom.setLevel(scaleValue);
    config.zoom_to_fit = false;
    zoomToFitMode = false;
    // toggleCheckbox(zoomToFitCheckbox, false);
}



let isPanning = false;
let initialMouseX, initialMouseY;
let initialScrollX, initialScrollY;

gantt.$task_data.addEventListener('mousedown', function(e) {
    gantt.$task_data.style.cursor = 'grabbing';

    initialMouseX = e.clientX;
    initialMouseY = e.clientY;

    const scrollState = gantt.getScrollState();
    initialScrollX = scrollState.x;
    initialScrollY = scrollState.y;

    isPanning = true;
});

document.addEventListener('mouseup', function() {
    gantt.$task_data.style.cursor = 'grab';
    isPanning = false;
});

document.addEventListener('mousemove', function(e) {
    if (!isPanning) return;

    const dx = e.clientX - initialMouseX;
    const dy = e.clientY - initialMouseY;

    gantt.scrollTo(initialScrollX - dx, initialScrollY - dy);
});

gantt.$task_data.style.cursor = 'grab';
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
            <a href="javascript:void(0);" class="card-expand"><i class="tf-icons ti ti-arrows-maximize ti-sm"></i></a>
          </li>
        </ul>
      </div>
    </div>
    <div class="card-body" id="gantt-chart-card">
      <div class="row">
        @if (!isset($project))
            <div class="col">
              {!! Form::label('projects', 'Projects') !!}
              {!! Form::select('projects[]', $projects, null, ['class' => 'form-select select2 gantt_filter', 'data-placeholder' => 'Projects', 'data-dropdownParent' => '$("#gantt-chart-card")']) !!}
            </div>
            <div class="col">
              {!! Form::label('programs', 'Programs') !!}
              {!! Form::select('programs', $programs, null, ['class' => 'form-select select2 gantt_filter', 'data-placeholder' => 'Programs']) !!}
            </div>
            <div class="col">
              {!! Form::label('project Status', 'Contract Status') !!}
              {!! Form::select('filter_status', [0 => 'All'] + $statuses, null, ['class' => 'form-select gantt_filter select2', 'data-placeholder' => 'Status']) !!}
            </div>
            <div class="col">
              {!! Form::label('contract_type', 'Contract Type') !!}
              {!! Form::select('contract_type', $contractTypes, null, ['class' => 'form-select select2 gantt_filter', 'data-placeholder' => 'Type']) !!}
            </div>
            <div class="col">
              {!! Form::label('companies[]', 'Client') !!}
              <select name="companies[]" id="" class="form-select select2 gantt_filter" data-placeholder='Client'>
                <option value="0">All</option>
                @if ($companies->where('type', 'Company')->count() > 0)
                  <optgroup label="Companies">
                    @forelse ($companies->where('type', 'Company') as $comp)
                      <option value="{{$comp->id}}">{{$comp->name}}</option>
                    @empty
                    @endforelse
                  </optgroup>
                @endif
                @if ($companies->where('type', 'Person')->count() > 0)
                  <optgroup label="Person">
                    @forelse ($companies->where('type', 'Person') as $comp)
                      <option value="{{$comp->id}}">{{$comp->name}}</option>
                    @empty
                    @endforelse
                  </optgroup>
                @endif
              </select>
            </div>
          @endif
      </div>
        <div class="d-flex justify-content-between align-items-center row pb-2">
          <div class="col-md-3">
            {!! Form::label('search_task', 'Search') !!}
            {!! Form::text('search_task', null, ['class' => 'form-control gantt_filter', 'placeholder' => 'Search']) !!}
          </div>

          <div class="{{!isset($project) ? 'mt-3' : ''}} col-3">
            <label>Zoom To</label>
            <select class="form-select" onchange="changeZoom(event)">
              <option value="years">Years</option>
              <option value="quarters">Quarters</option>
              <option value="months">Months</option>
              <option selected value="weeks">Weeks</option>
              <option value="days">Days</option>
              <option value="hours">Hours</option>
            </select>
          </div>
          <div class="col-md-7 d-flex justify-content-between pt-4">
            <button class='btn btn-primary zoom_toggle' onclick="toggleMode(this)">Zoom to Fit</button>
            <button class="btn btn-primary" onclick="zoom_in()">Zoom In</button>
            <button class="btn btn-primary" onclick="zoom_out()">Zoom Out</button>
            <label class="switch switch-lg">
              <input type="checkbox" class="switch-input config-input" onchange="changeCollapse(this)">
              <span class="switch-toggle-slider">
                <span class="switch-on">
                  <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                  <i class="ti ti-x"></i>
                </span>
              </span>
              <span class="switch-label">Collapse</span>
            </label>
            <label class="switch switch-lg">
              <input type="checkbox" class="switch-input config-input" onchange="changeToday(this)" checked>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                  <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                  <i class="ti ti-x"></i>
                </span>
              </span>
              <span class="switch-label">Today</span>
            </label>
            <label class="switch switch-lg">
              <input type="checkbox" class="switch-input config-input" onchange="changeSidebar(this)" checked>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                  <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                  <i class="ti ti-x"></i>
                </span>
              </span>
              <span class="switch-label">SideBar</span>
            </label>
            <label class="switch switch-lg">
              <input type="checkbox" class="switch-input config-input" onchange="toggleChart(this)" checked>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                  <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                  <i class="ti ti-x"></i>
                </span>
              </span>
              <span class="switch-label">Chart</span>
            </label>
          </div>
        </div>
        <div id="gantt_here" style='width:100%; height:calc(100vh - 52px); position: relative;'></div>
    </div>
  </div>
</div>
@endsection
