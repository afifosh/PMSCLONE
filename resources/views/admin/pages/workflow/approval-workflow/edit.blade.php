@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Approval Workflow')

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
<script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
@can(true)
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{route('admin.approval-workflows.update', $workflow)}}" method="POST">
          @csrf
          @method('PUT')
          <h3>Edit Workflow</h3>
          <div class="pb-1 border rounded" style="background-color: #f1f0f2;">
            <div class="row p-3">
              <div class="col-12">
                <label class="form-label">Workflow Name</label>
                {{ Form::text('workflow_name', $workflow->name, ['class' => 'form-control', 'placeholder' => 'Workflow Name']) }}
              </div>
            </div>
          </div>

          <div class="row g-3 form-repeater">
            @forelse ($levels as $level)
              <div data-repeater-list="group-a">
                <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
                  <h6 class="mb-0">Level {{$level->order}}</h6>
                  <div class="row">
                    <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                      <label class="form-label">Level Name</label>
                     {{ Form::text('name['.$level->id.']', $level->name, ['class' => 'form-control', 'placeholder' => 'Level Name']) }}
                    </div>
                    <div class="mb-3 col-lg-6 col-xl-9 col-12 mb-0">
                      @php
                          $optionParameters = collect($admins)->mapWithKeys(function ($item) {
                              return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
                          })->all();
                      @endphp
                      <label class="form-label">Approvers</label>
                      {!! Form::select('approvers['.$level->id.'][]', $admins->pluck('email', 'id'), $level->approvers->pluck('id')->toArray(),
                      ['class' => 'form-select select2User', 'multiple', 'data-placeholder' => 'Approvers'], $optionParameters) !!}
                    </div>
                  </div>
                </div>
              </div>
            @empty
            @endforelse
          </div>
          <div class="mb-0 mt-3 text-end">
            <button class="btn btn-primary btn-next" data-form="ajax-form"> <span class="align-middle">Update</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endcan

@endsection
@push('scripts')
@endpush
