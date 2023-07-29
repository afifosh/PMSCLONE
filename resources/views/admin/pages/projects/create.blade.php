@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Create Project')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
@can(true)
<h3>Create Project</h3>
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{route('admin.projects.store')}}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Project Name</label>
                <input type="text" name="name" id="name" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="program_id" class="form-label">Program</label>
                {!! Form::select('program_id', $programs, null, ['class' => 'form-select select2']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                {!! Form::select('category_id', $categories, null, ['class' => 'form-select select2']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                {!! Form::select('tags[]', [], null, ['class' => 'form-select select2', 'multiple', 'data-tags' => 'true']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control flatpickr">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" name="deadline" id="deadline" class="form-control flatpickr">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="budget" class="form-label">Budget</label>
                <input type="number" name="budget" id="budget" placeholder="Budget" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="refrence_id" class="form-label">Refrence ID</label>
                <input type="text" name="refrence_id" id="refrence_id" placeholder="Refrence ID" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                {!! Form::select('status', $statuses, null, ['class' => 'form-select select2']) !!}
              </div>
            </div>

            @php
              $optionParameters = collect($members)->mapWithKeys(function ($item) {
                  return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
              })->all();
            @endphp
            <div class="col-md-6">
              <div class="mb-3">
                <label for="members" class="form-label">Members</label>
                {!! Form::select('members[]', $members->pluck('email', 'id'), null, ['class' => 'form-select select2User', 'data-placeholder' => 'Select members', 'multiple' => 'multiple'], $optionParameters) !!}
              </div>
            </div>
            <div class="col-md-12">
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="5"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="progress" name="is_progress_calculatable" checked>
                <label class="form-check-label" for="progress">Calculate progress through tasks</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="create_chat_group" name="create_chat_group" checked>
                <label class="form-check-label" for="create_chat_group">Auto create a group chat for the project</label>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <a href="{{route('admin.projects.index')}}" class="btn btn-secondary me-2">Cancel</a>
            <button type="button" data-form="ajax-form" class="btn btn-primary">Create</button>
          </div>
      </div>
    </div>
  </div>
@endcan

@endsection
@push('scripts')
@endpush
