<style>
#checklist-input {
  resize: none;
  overflow: hidden;
  min-height: 40px;
  height: auto;
}
</style>
<div class="row d-flex">
  <form action="{{route('admin.projects.tasks.checklist-items.store', ['task' => $task->id, 'project' => $task->project_id])}}">
    <div class="my-2">
      <div class="flex-grow-1  me-1">
        <textarea class="form-control" name="title" placeholder="What you wanna do today?" id="checklist-input" rows="1"></textarea>
      </div>
      <div class="row mt-2">
        <div class="col-6"></div>
        <div class="d-flex justify-conent-end col-6">
          {!! Form::select('assigned_to', $task->assignees->pluck('email', 'id')->prepend('Assigned To', ''), null, ['class' => 'form-select globalOfSelect2', 'id' => 'assigned_to']) !!}
          <input type="date" id="due_date" name="due_date" class="form-control mx-1">
          <button class="btn btn-primary  align-self-center" data-form="ajax-form">Add</button>
        </div>
      </div>
    </div>
  </form>
</div>

<ul class="p-0 list-group list-group-flush" id="sortable">
  @include('admin.pages.projects.tasks.checklist-index')
</ul>
<script>
  $(document).on('input', '#checklist-input', function() {
    this.style.height = 'auto'; // Reset the height to auto
    this.style.height = this.scrollHeight + 'px'; // Set the height to the scroll height
  });

  $(document).on('click', '.checklist-status', function(){
    var checklist_id = $(this).parents('li').data('checklist-id');
    var task_id = $(this).parents('li').data('task-id');
    var status = $(this).is(':checked') ? 1 : 0;
    var url = "{{route('admin.projects.tasks.checklist-items.update-status', ['task' => ':task_id', 'project' => ':project_id', 'checklist_item' => ':checklist_id'])}}";
    url = url.replace(':checklist_id', checklist_id);
    url = url.replace(':task_id', task_id);
    $.ajax({
      url: url,
      type: "PUT",
      data: {
        status: status,
      },
      success: function(res){
        reload_task_checklist();
      }
    });
  });

  function reset_checklist_form(){
    $('#checklist-input').val('');
    $('#assigned_to').val('');
    $('#due_date').val('');
  }

  function handle_deleted_checklist(url){
    reload_task_checklist();
    toast_undo(url);
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
</script>
