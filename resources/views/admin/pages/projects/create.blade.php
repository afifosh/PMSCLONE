@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', $project->id ? 'Edit Project' : 'Create Project')

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
<h3>{{$project->id ? 'Edit' : 'Create'}} Project</h3>
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        @if ($project->id)
            {!! Form::model($project, ['route' => ['admin.projects.update', ['project' => $project]], 'method' => 'PUT']) !!}
        @else
            {!! Form::model($project, ['route' => ['admin.projects.store'], 'method' => 'POST']) !!}
        @endif
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Project Name</label>
                {!! Form::text('name', $project->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="companies" class="form-label">Company</label>
                {!! Form::select('companies[]', $companies, $project->companies, ['class' => 'form-select select2', 'multiple' => 'multiple']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="program_id" class="form-label">Program</label>
                {!! Form::select('program_id', $programs, $project->program_id, ['class' => 'form-select select2']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                {!! Form::select('category_id', $categories, $project->category_id, ['class' => 'form-select select2']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                {!! Form::select('tags[]', $project->tags ? array_combine($project->tags, $project->tags) : [], $project->tags, ['class' => 'form-select select2', 'multiple', 'data-tags' => 'true']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                {!! Form::date('start_date', $project->start_date, ['class' => 'form-control flatpickr']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="deadline" class="form-label">Deadline</label>
                {!! Form::date('deadline', $project->deadline, ['class' => 'form-control flatpickr']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="budget" class="form-label">Budget</label>
                {!! Form::number('budget', $project->budget, ['class' => 'form-control', 'placeholder' => 'Budget']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="refrence_id" class="form-label">Refrence ID</label>
                {!! Form::text('refrence_id', $project->refrence_id, ['class' => 'form-control', 'placeholder' => 'Refrence ID']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                {!! Form::select('status', $statuses, $project->status, ['class' => 'form-select select2']) !!}
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
                {!! Form::textarea('description', $project->description, ['class' => 'form-control', 'rows' => '5']) !!}
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                {!! Form::checkbox('is_progress_calculatable', 1, $project->is_progress_calculatable, ['id' => 'progress', 'class' => 'form-check-input']) !!}
                <label class="form-check-label" for="progress">Calculate progress through tasks</label>
              </div>
              <div class="form-check">
                {!! Form::checkbox('create_chat_group', 1, $project->create_chat_group, ['id' => 'create_chat_group', 'class' => 'form-check-input']) !!}
                <label class="form-check-label" for="create_chat_group">Auto create a group chat for the project</label>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <a href="{{route('admin.projects.index')}}" class="btn btn-secondary me-2">Cancel</a>
            <button type="button" data-form="ajax-form" class="btn btn-primary">{{$project->id ? __('Update') : __('Create')}}</button>
          </div>
      </div>
    </div>
  </div>

@endsection
@push('scripts')
@endpush
