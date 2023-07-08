@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Tasks')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
{{-- <script src="{{asset('assets/js/extended-ui-drag-and-drop.js')}}"></script> --}}
<script>
  function initSortable() {
    var sortable = Sortable.create(document.getElementById('sortable'), {
      group: 'shared',
      animation: 150,
      dataIdAttr: 'data-checklist-id',
      onSort: function (/**Event*/evt) {
        var task_id = $(evt.item).attr('data-task-id');
        var project_id = $(evt.item).attr('data-project-id');
        var url = "{{route('admin.projects.tasks.checklist-items.update-order', ['task' => ':task_id', 'project' => ':project_id'])}}";
        url = url.replace(':task_id', task_id);
        url = url.replace(':project_id', project_id);
        $.ajax({
          url: url,
          type: "PUT",
          data: {
            order: sortable.toArray(),
          },
          success: function(res){
          }
        });
      },

    });
  }
  function initDropZone(){
    const previewTemplate = `<div class="dz-preview dz-file-preview">
        <div class="dz-details">
          <div class="dz-thumbnail">
            <img data-dz-thumbnail>
            <span class="dz-nopreview">No preview</span>
            <div class="dz-success-mark"></div>
            <div class="dz-error-mark"></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="progress">
              <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
            </div>
          </div>
          <div class="dz-filename" data-dz-name></div>
          <div class="dz-size" data-dz-size></div>
        </div>
        </div>`;
    $('.dropzone').each(function(){
      var $this = this;
      const dropzone = new Dropzone($this, {
        // const dropzoneMulti = new Dropzone('#dropzone-multi', {
        previewTemplate: previewTemplate,
        parallelUploads: 4,
        maxFiles: 100,
        addRemoveLinks: true,
        chunking: false,
        method: "POST",
        maxFilesize: 100,
        chunkSize: 1900000,
        autoProcessQueue : true,
        // If true, the individual chunks of a file are being uploaded simultaneously.
        parallelChunkUploads: true,
        retryChunks: true,
        acceptedFiles: 'text/plain,application/*,image/*,video/*,audio/*',
        url: $($this).data('upload-url'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
          $($($this).data('response')).val(response.data.file_path);
          $($($this).data('response')+'is_new').val(1);
            console.log(response);
            // remote this uploaded file from preview
            this.removeFile(file);
        },
        init: function(){
            /* Called once the file has been processed. It could have failed or succeded */
            this.on("complete", function(file){

            });
            /* Called after the file is uploaded and sucessful */
            this.on("sucess", function(file){

            });
            /* Called before the file is being sent */
            this.on("sending", function(file){
            });

            this.on("addedfile", function() {
              // if (this.files[1]!=null){
              //   this.removeFile(this.files[0]);
              // }
            });

            this.on("removedfile", function() {
              $($($this).data('response')).val('');
            });

            this.on("maxfilesexceeded", function(file){
                alert("No more files please!");
            });

            this.on("error", function(file, errorMessage, xhr){
              // Check if the response is a validation error
              if (xhr.status === 422) {
                // Parse the validation errors from the response
                var errors = JSON.parse(xhr.responseText).errors;

                // Loop through the validation errors and add them to the file preview
                $.each(errors, function(key, value) {
                  var error = value[0];
                  var dzError = $('<div>').addClass('dz-error-message').text(error);
                  $(file.previewElement).append(dzError);
                });
              }
            })
        }
      });
    });
  }
  function add_checklist(task_id, elm){
    var checklist = `<li class="list-group-item drag-item cursor-move d-flex mt-1" data-task-id="${task_id}" data-project-id="0">
          <div class="form-check form-check-success mt-2">
            <input class="form-check-input rounded-circle" type="checkbox" value="1">
          </div>
          <div class="">
              <input type="text" name="" id="" class="form-control">
          </div>
          <div class="mt-2 ms-3">
              <i class="fa-regular fa-xl fa-user cursor-pointer"></i>
              <i class="fa-solid fa-xl fa-copy cursor-pointer"></i>
              <i onclick="delete_checklist(this)" class="fa-regular fa-xl fa-trash-can cursor-pointer"></i>
          </div>
      </li>`;

      $(elm).parent().parent().parent().find('.list-group').append(checklist);
      // focus on the new checklist
      $(elm).parent().parent().parent().find('.list-group li:last-child input').focus();
  }

  // listen for focus out on the checklist input
  $(document).on('focusout', '.list-group li input[type="text"]', function(){
    handleChecklist($(this));
  });

  // listen for enter key on the checklist input
  $(document).on('keyup', '.list-group li input[type="text"]', function(e){
    if(e.keyCode == 13){
      $(this).blur();
    }
  });

  // listen for click on the checklist checkbox
  $(document).on('click', '.list-group li input[type="checkbox"]', function(){
    handleChecklist($(this).parent().parent().find('input[type="text"]'));
  });

  function handleChecklist(elm){
    elm = $(elm).parent().parent().find('input[type="text"]');
    if($(elm).val() == ''){
      $(elm).parent().parent().remove();
      return;
    }
    var task_id = $(elm).parent().parent().attr('data-task-id');
    var project_id = $(elm).parent().parent().attr('data-project-id');
    var checklist_id = $(elm).parent().parent().attr('data-checklist-id');
    var checklist_status = $(elm).parent().parent().find('input[type="checkbox"]').is(':checked');
    var checklist_name = $(elm).val();
    if(checklist_id == undefined){
      // create new checklist
      var url = "{{route('admin.projects.tasks.checklist-items.store', ['task' => ':task_id', 'project' => ':project_id'])}}";
      url = url.replace(':task_id', task_id);
      url = url.replace(':project_id', project_id);
      $.ajax({
        url: url,
        type: "POST",
        data: {
          title: checklist_name,
          status: checklist_status,
        },
        success: function(res){
          $(elm).parent().parent().attr('data-checklist-id', res.data.id);
        }
      });
    }else{
      // update checklist
      var url = "{{route('admin.projects.tasks.checklist-items.update', ['task' => ':task_id', 'project' => ':project_id', 'checklist_item' => ':checklist_id'])}}";
      url = url.replace(':task_id', task_id);
      url = url.replace(':project_id', project_id);
      url = url.replace(':checklist_id', checklist_id);

      $.ajax({
        url: url,
        type: "PUT",
        data: {
          title: checklist_name,
          status: checklist_status,
        },
        success: function(data){
        }
      });
    }
  }

  function delete_checklist(elm) {
    var checklist_id = $(elm).parent().parent().attr('data-checklist-id');
    if(checklist_id == undefined || checklist_id == ''){
      $(elm).parent().parent().remove();
      return;
    }else{
      // prompt user to confirm
      if(confirm('Are you sure you want to delete this checklist?')){
        $(elm).parent().parent().remove();
        var url = "{{route('admin.projects.tasks.checklist-items.destroy', ['task' => ':task_id', 'project' => ':project_id', 'checklist_item' => ':checklist_id'])}}";
        url = url.replace(':checklist_id', checklist_id);
        $.ajax({
          url: url,
          type: "DELETE",
          success: function(data){
          }
        });
      }
    }
  }
</script>
@endsection

@section('content')
@include('admin.pages.projects.navbar', ['tab' => 'tasks'])
@can(true)
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <h4>Tasks Summary</h4>
          <div class="row">
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 'not started')->first()->task_count ?? 0}}</h5>
              <span class="">Not Started</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 'in progress')->first()->task_count ?? 0}}</h5>
              <span class="text-primary">In Progress</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 'on hold')->first()->task_count ?? 0}}</h5>
              <span class="text-warning">On Hold</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 'awaiting feedback')->first()->task_count ?? 0}}</h5>
              <span class="text-muted">Awaiting Feedback</span>
            </div>
            <div class="col-2 d-flex">
              <h5 class="mx-3">{{$summary->where('status', 'completed')->first()->task_count ?? 0}}</h5>
              <span class="text-success">Completed</span>
            </div>
        </div>
        <hr class="mt-2">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>
@endcan

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
