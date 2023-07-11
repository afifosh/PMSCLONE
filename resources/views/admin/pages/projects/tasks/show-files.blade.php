<div class="row">
  <div class="col-7 mt-3">
    {{-- <h4>Attachments</h4> --}}
    <div class="row" id="files-list">
      @include('admin.pages.projects.tasks.files-index')
    </div>
  </div>
  <div class="col-md-5 task-single-col-right rounded">
    <div class="dropzone needsclick bg-white my-2" data-upload-url="{{ route('admin.projects.tasks.files.store',['project' => $task->project_id, 'task' => $task->id])}}" data-response="#{{'fields_'.$task['id']}}">
      <div class="dz-message needsclick">
        <small class="h6"> Drop files here to upload </small>
      </div>
    </div>
  </div>
</div>
<script>
  function reload_files_list() {
    var url = "{{route('admin.projects.tasks.files.index', ['project' => ':project_id', 'task' => $task])}}";
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#files-list').html(data.data.view_data);
      }
    });
  }
</script>
