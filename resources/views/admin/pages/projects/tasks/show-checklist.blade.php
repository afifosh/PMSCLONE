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
    <div class="d-flex my-2">
      <div class="flex-grow-1  me-1">
        <textarea class="form-control" name="title" placeholder="What you wanna do today?" id="checklist-input" rows="1"></textarea>
      </div>
      <div>
        <button class="btn btn-primary  align-self-center" data-form="ajax-form">Add</button>
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
    var url = "{{route('admin.projects.tasks.checklist-items.update', ['task' => ':task_id', 'project' => ':project_id', 'checklist_item' => ':checklist_id'])}}";
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
  }

  function delete_checklist(checklist_id, task_id) {
      // prompt user to confirm
      if(confirm('Are you sure you want to delete this checklist?')){
        var url = "{{route('admin.projects.tasks.checklist-items.destroy', ['task' => ':task_id', 'project' => ':project_id', 'checklist_item' => ':checklist_id'])}}";
        url = url.replace(':checklist_id', checklist_id);
        $.ajax({
          url: url,
          type: "DELETE",
          success: function(data){
            reload_task_checklist();
          }
        });
      }
  }

  function reload_task_checklist(){
    var url = "{{route('admin.projects.tasks.checklist-items.index', ['task' => $task->id, 'project' => $task->project_id])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        console.log(data.data.view_data);
        $('#sortable').html(data.data.view_data);
      }
    });
  }
</script>
