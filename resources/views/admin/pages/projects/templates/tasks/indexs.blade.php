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
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/tasks/style.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script src="{{asset('assets/js/custom/admin-task-templates.js')}}"></script>
{{-- <script src={{asset('assets/js/custom/ajax.js')}}></script> --}}
{{-- <script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script> --}}
{{-- <script src="{{asset('assets/js/custom/toastr-helpers.js')}}"></script> --}}
@endsection

@section('content')
<div class="d-flex justify-content-between">
  <h4>{{$project_template->name}}</h4>
  <button class="btn btn-primary btn-sm align-self-start" data-toggle="ajax-modal" data-title="Add Task" data-href="{{route('admin.project-templates.tasks.create', [$project_template])}}">{{ __('Add Task') }}</button>
</div>
  @forelse ($project_template->taskTemplates as $task)
    <div class="col-12">
      <div class="card card-action mb-4" data-href="{{ route('admin.project-templates.tasks.check-items.index', [$project_template, $task])}}">
        <div class="card-header mb-0 d-sm-flex justify-content-between">
          <div class="card-action-title">
            <h6 class="mb-0">{{$task->subject}}</h6>
            <small>{{$task->description}}</small>
          </div>
          <div class="card-action-element">
            <ul class="list-inline mb-0">
              <li class="list-inline-item">
                <a href="javascript:void(0);" class="card-collapsible"><i class="tf-icons ti ti-chevron-right scaleX-n1-rtl ti-sm"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="javascript:void(0);" data-href="{{route('admin.project-templates.tasks.edit', [$project_template, $task])}}" data-toggle="ajax-modal" data-title="Edit Task"><i class="tf-icons ti ti-pencil scaleX-n1-rtl ti-sm"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="javascript:void(0);" data-href="{{route('admin.project-templates.tasks.check-items.create', [$project_template, $task])}}" data-toggle="ajax-modal" data-title="Add Check Item"><i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="javascript:void(0);" data-href="{{route('admin.project-templates.tasks.destroy', [$project_template, $task])}}" data-toggle="ajax-delete"><i class="tf-icons ti ti-trash scaleX-n1-rtl ti-sm"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="javascript:void(0);" id="reload-check-items__{{$task->id}}" class="card-reload"><i class="tf-icons ti ti-rotate-clockwise-2 scaleX-n1-rtl ti-sm"></i></a>
              </li>
            </ul>
          </div>
        </div>
        <div class="pb-3 collapse show">
            @include('admin.pages.projects.templates.tasks.check-items.index')
        </div>
      </div>
    </div>
  @empty
  @endforelse
@endsection
