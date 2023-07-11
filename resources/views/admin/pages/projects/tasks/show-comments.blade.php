<div class="row mt-3">
  <div>
    <textarea name="test" class="init-editor" id="tiny" cols="30" rows="10"></textarea>
  </div>
  <div class="d-flex justify-content-end my-2">
    <button class="btn btn-success" onclick="create_comment({{$task->id}})">Add Comment</button>
  </div>
  <div id="comments-list">
    @include('admin.pages.projects.tasks.comments-index')
  </div>
</div>

<script>
  function create_comment(task_id){
    var comment = tinymce.get('tiny').getContent();
    if(comment == ''){
      return;
    }
    var url = "{{route('admin.projects.tasks.comments.store', ['task' => ':task_id', 'project' => ':project_id'])}}";
    url = url.replace(':task_id', task_id);
    $.ajax({
      url: url,
      type: "POST",
      data: {
        comment: comment,
      },
      success: function(res){
        tinymce.get('tiny').setContent('');
        reload_task_comments(task_id);
      }
    });
  }

  function remove_task_comment(comment_id){
    // confimation prompt
    if(!confirm('Are you sure you want to delete this comment?')){
      return;
    }
    var url = "{{route('admin.projects.tasks.comments.destroy', ['task' => ':task_id', 'project' => ':project_id', 'comment' => ':comment_id'])}}";
    url = url.replace(':comment_id', comment_id);
    $.ajax({
      url: url,
      type: "DELETE",
      success: function(res){
        reload_task_comments({{$task->id}});
      }
    });
  }

  function reload_task_comments(task_id) {
    var url = "{{route('admin.projects.tasks.comments.index', ['project' => ':project_id', 'task' => ':task_id'])}}";
    url = url.replace(':task_id', task_id);
    $.ajax({
      url: url,
      type: "GET",
      success: function(data){
        $('#comments-list').html(data.data.view_data);
      }
    });
  }
</script>
