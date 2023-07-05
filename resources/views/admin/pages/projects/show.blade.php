@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', $project->name)

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
  @include('admin.pages.projects.navbar', ['tab' => 'overview'])

  <div class="row">
    <div class="mt-3  col-6">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <span class="fw-bold">Project Progress 60.3%</span>
            <div class="mt-2">
              @include('admin._partials.sections.progressBar', ['perc' => 60.3, 'color' => 'success'])
            </div>
            <hr class="my-3">
            <span class="fw-bold">Overview</span>
            <div class="col-md-6 p-3">
              <div>Project #</div>
              <span class="fw-bold">{{$project->id}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Project Name</div>
              <span class="fw-bold">{{$project->name}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Category</div>
              <span class="fw-bold">{{$project->category->name}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Program</div>
              <span class="fw-bold">{{$project->program->name}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Start Date</div>
              <span class="fw-bold">{{$project->start_date}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Deadline</div>
              <span class="fw-bold">{{$project->deadline}}</span>
            </div>
            <div class="col-md-6 p-3">
              <div>Members</div>
              @include('admin._partials.sections.user-avatar-group', ['users' => $project->members])
            </div>
            <div class="col-md-6 p-3">
              <div>Status</div>
              <span class="fw-bold">{{$project->status}}</span>
            </div>
            <div class="col-md-12 p-3">
              <div>Tags</div>
              @forelse ($project->tags as $tag)
                <span class="fw-bold">{{$tag}}</span>
              @empty
              @endforelse
            </div>
            <div class="col-md-12 p-3">
              <div>Description</div>
              <span class="fw-bold">{{$project->description}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3  col-3">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-9">
              <span class="fw-bold">5 / 7 Open Tasks</span>
            </div>
            <div class="col-md-3 text-end">
              <i class="fa-regular fa-check-circle fa-xl" aria-hidden="true"></i>
            </div>
            <div class="mt-2">
              <span>60.3%</span>
              @include('admin._partials.sections.progressBar', ['perc' => 60.3, 'color' => 'success'])
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3  col-3">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-9">
              <span class="fw-bold">5 / 7 Days Left</span>
            </div>
            <div class="col-md-3 text-end">
              <i class="fa-regular fa-calendar-check fa-xl" aria-hidden="true"></i>
            </div>
            <div class="mt-2">
              <span>60.3%</span>
              @include('admin._partials.sections.progressBar', ['perc' => 60.3, 'color' => 'success'])
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
@push('scripts')
@endpush
