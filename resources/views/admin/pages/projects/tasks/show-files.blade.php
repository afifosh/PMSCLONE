<div class="row">
  <div class="col-7 mt-3">
    {{-- <h4>Attachments</h4> --}}
    <div class="row" id="files-list">
      @includeWhen(request()->tab == 'files' ,'admin.pages.projects.tasks.files-index')
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
