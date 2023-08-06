@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
{{-- <link rel="stylesheet" href="{{asset('assets/css/tasks/style.css')}}" /> --}}
@livewireStyles
<x-comments::styles />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/chartjs/chartjs.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
{{-- <script src="{{asset('assets/js/extended-ui-drag-and-drop.js')}}"></script> --}}
<script>
  $(document).ready(function () {
    Dropzone.options.projectFilesUpload = false;
    Dropzone.options.projectExpenseForm = false;

  });

  function initEditor(){
    // destroy old editor
  }
  function initSortable() {
    $('.checklist-drag').show();
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
            reload_files_list();
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

  function remove_task_attachment(id, task_id){
    if(confirm('Are you sure you want to delete this attachment?')){
      var url = "{{route('admin.projects.tasks.files.destroy', ['task' => ':task_id', 'project' => ':project_id', 'file' => ':file_id'])}}";
      url = url.replace(':file_id', id);
      url = url.replace(':task_id', task_id);
      $.ajax({
        url: url,
        type: "DELETE",
        success: function(data){
          $('[data-task-file-id="'+id+'"]').remove();
        }
      });
    }
  }
</script>
@endsection

@section('content')
<h4>Dashboard</h4>
<div class="row">
  <div class="col-9">
    <h5>My Tasks</h5>
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4 user_plan">
            <select name="filter_projects[]" class="form-select select2" multiple data-placeholder="Project">
              @forelse ($projects as $id => $project)
                <option value="{{$id}}">{{$project}}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4 user_status">
            <select name="filer_status[]" class="form-select select2" multiple data-placeholder="Task Status">
              @forelse ($task_statuses as $status)
                <option value="{{$status}}">{{$status}}</option>
              @empty
              @endforelse
            </select>
          </div>
        </div>
      </form>
      <div class="card-body">
        <div class="row">
        {{$dataTable->table()}}
        </div>
      </div>
    </div>
  </div>
  <div class="col-3">
    <h5>Project Statistics</h5>
    <div class="card">
      <div class="card-body">
        <div>
          <canvas id="projects-statuses-chart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      const ctx = document.getElementById('projects-statuses-chart');
      const data = {!! json_encode($projectsStatusesChartData) !!};
      new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {

        }
      });
    </script>
    <script>
      function view_task_from_url()
      {
        var urlParams = new URLSearchParams(window.location.search);
        var viewParam = urlParams.get('view');

        if (viewParam !== null) {
          var currentURL = window.location.href;
          var link = $('<a>', { href: currentURL });

          // Get the pathname from the URL using jQuery's attr() method
          var pathname = link.prop('pathname');

          // Split the pathname by '/'
          var pathParts = pathname.split('/');

          // Find the index of 'projects' in the pathParts array
          var projectsIndex = $.inArray('projects', pathParts);

          // Get the value after 'projects' (i.e., the {id})
          var projectId = pathParts[projectsIndex + 1];
          if($('[data-href="{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam +'"][data-toggle="ajax-modal"]').length == 0){
            var hiddenButton = $('<button>', {
            data: {
              toggle: 'ajax-modal',
              href: '{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam,
            },
            style: 'display: none;'
          });

          // Append the hidden button to the DOM
          $('body').append(hiddenButton);
          }

          // Trigger a click event on the hidden button
          $('[data-href="{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam +'"][data-toggle="ajax-modal"]').data('href', '{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam + '?tab=comments').click();
          // revert the elements' url after the click
          setTimeout(() => {
            $('[data-href="{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam + '?tab=comments"][data-toggle="ajax-modal"]').data('href', '{{url('/')}}/admin/projects/'+projectId+'/tasks/'+ viewParam);
          }, 1000);
        }

      }

      $(document).ready(function () {
        window.oURL = window.location.href;
      });

      $('#globalModal').on('show.bs.modal', function () {
        $(this).find('#globalModalBody').each(function () {
          Livewire.rescan(this);
          Alpine.initTree(this);
        });

        setTimeout(function () {
          history.replaceState(null, null, oURL);
        }, 1000);
      });
    </script>
    @livewireScripts
    <x-comments::scripts />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["tasks-datatable"].draw();
          });

          $('#tasks-datatable').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
