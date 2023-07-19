<style>
  .nav-tabs-shadow {
    box-shadow : none !important;
  }
</style>
<div class="row">
    <div class="nav-align-top nav-tabs-shadow">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" onclick="reload_task_summary();">
          <button type="button" class="nav-link {{request()->tab == null || request()->tab == 'summary' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-summary" aria-controls="navs-top-summary" aria-selected="true">Summary</button>
        </li>
        <li class="nav-item" onclick="reload_task_checklist();">
          <button type="button" class="nav-link {{request()->tab == 'checklist' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-checklist" aria-controls="navs-top-checklist" aria-selected="false">Checklist</button>
        </li>
        <li class="nav-item" onclick="reload_task_files();">
          <button type="button" class="nav-link {{request()->tab == 'files' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-files" aria-controls="navs-top-files" aria-selected="false">Files</button>
        </li>
        <li class="nav-item" onclick="reload_logs_list();">
          <button type="button" class="nav-link {{request()->tab == 'activities' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-activities" aria-controls="navs-top-activities" aria-selected="false">Activities</button>
        </li>
        <li class="nav-item" onclick="reload_task_comments();">
          <button type="button" class="nav-link {{request()->tab == 'comments' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comments" aria-controls="navs-top-comments" aria-selected="false">Comments</button>
        </li>
      </ul>
      <div class="tab-content p-0">
        <div class="tab-pane fade {{request()->tab == null || request()->tab == 'summary' ? 'show active' : ''}}" id="navs-top-summary" role="tabpanel">
          @includeWhen(request()->tab == null || request()->tab == 'summary','admin.pages.projects.tasks.show-summary')
        </div>
        <div class="tab-pane fade {{request()->tab == 'checklist' ? 'show active' : ''}}" id="navs-top-checklist" role="tabpanel">
          @include('admin.pages.projects.tasks.show-checklist')
        </div>
        <div class="tab-pane fade {{request()->tab == 'files' ? 'show active' : ''}}" id="navs-top-files" role="tabpanel">
          @include('admin.pages.projects.tasks.show-files')
        </div>
        <div class="tab-pane fade {{request()->tab == 'activities' ? 'show active' : ''}}" id="navs-top-activities" role="tabpanel">
          @includeWhen(request()->tab == 'activities', 'admin.pages.projects.tasks.show-activities')
        </div>
        <div class="tab-pane fade {{request()->tab == 'comments' ? 'show active' : ''}}" id="navs-top-comments" role="tabpanel">
          @includeWhen(request()->tab == 'comments', 'admin.pages.projects.tasks.show-comments')
        </div>
      </div>
    </div>
</div>
<script>
  function create_reminder(id = null){
    $('[name="reminder_id"]').val(null);
    $('#create-reminder-form').toggle();
  }

   function update_reminder_form(){
    var form = $('#create-reminder-form');
    form.hide();
    var reminder_id = form.find('[name="reminder_id"]').val();
    var remind_at = form.find('[name="remind_at"]').val();
    var recipient_id = form.find('[name="recipient_id"]').val();
    var description = form.find('[name="description"]').val();

    if(reminder_id == null){
      form.before('<div class="alert alert-info">Reminder will be created</div>')
    }
  }

  function reload_task_summary(){
    var url = "{{route('admin.projects.tasks.show', ['project' => $task->project_id, 'task' => $task->id, 'type' =>'summary-list'])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-summary').html(data.data.view_data);
      }
    });
  }

  function reload_task_checklist(){
    var url = "{{route('admin.projects.tasks.checklist-items.index', ['task' => $task->id, 'project' => $task->project_id])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#sortable').html(data.data.view_data);
      }
    });
  }

  function reload_task_files() {
    var url = "{{route('admin.projects.tasks.files.index', ['project' => ':project_id', 'task' => $task])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#files-list').html(data.data.view_data);
      }
    });
  }

  function reload_logs_list()
  {
    var url = "{{route('admin.projects.tasks.show', ['project' => $task->project_id, 'task' => $task->id, 'type' =>'activities-list'])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-activities').html(data.data.view_data);
      }
    });
  }

  function reload_task_comments(){
    var url = "{{route('admin.projects.tasks.show', ['project' => $task->project_id, 'task' => $task->id, 'type' =>'comments-list'])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#navs-top-comments').html(data.data.view_data);
          Livewire.rescan(document.getElementById('navs-top-comments'));
          Alpine.initTree(document.getElementById('navs-top-comments'));
        setTimeout(function () {
          history.replaceState(null, null, oURL);
        }, 1000);
      }
    });
  }
</script>
