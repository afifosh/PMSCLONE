<style>
#checklist-input {
  resize: none;
  overflow: hidden;
  min-height: 40px;
  height: auto;
}
</style>
<div class="row d-flex">
  <div class="d-flex my-2">
    <textarea class="form-control me-1" placeholder="What you wanna do today?" id="checklist-input" rows="1"></textarea>
    <button class="btn btn-success  align-self-center" onclick="add_checklist({{$task->id}}, {{$task->project_id}}, this)">Add</button>
  </div>
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
    var status = $(this).is(':checked');
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
        reload_task_checklist(task_id);
      }
    });
  });

  function add_checklist(task_id, project_id, elm){
    elm = $(elm).parents().find('#checklist-input');
    if($(elm).val() == ''){
      return;
    }
    var checklist_name = $(elm).val();
    // create new checklist
    var url = "{{route('admin.projects.tasks.checklist-items.store', ['task' => ':task_id', 'project' => ':project_id'])}}";
    url = url.replace(':task_id', task_id);
    url = url.replace(':project_id', project_id);
    $.ajax({
      url: url,
      type: "POST",
      data: {
        title: checklist_name,
        status: '',
      },
      success: function(res){
        $(elm).val('');
        reload_task_checklist(task_id);
      }
    });
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
            reload_task_checklist(task_id);
          }
        });
      }
  }

  function reload_task_checklist(task_id){
    var url = "{{route('admin.projects.tasks.checklist-items.index', ['task' => ':task_id', 'project' => ':project_id'])}}";
    url = url.replace(':task_id', task_id);
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
