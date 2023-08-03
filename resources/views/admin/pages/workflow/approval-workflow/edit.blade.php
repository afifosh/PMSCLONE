@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts.layoutMaster')

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
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-extras.js')}}"></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <form class="form-repeater" action="{{route('admin.workflows.update', $workflow)}}" method="POST">
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
          <hr>
          <div data-repeater-list="level">
          @forelse ($levels as $level)
              <div data-repeater-item>
                <div class="row">
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label">Level Name</label>
                  {{ Form::text('level[][name]', $level->name, ['class' => 'form-control', 'placeholder' => 'Level Name']) }}
                  {!! Form::hidden('level[][id]', $level->id) !!}
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-7 col-12 mb-0">
                    @php
                        $optionParameters = collect($admins)->mapWithKeys(function ($item) {
                            return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
                        })->all();
                    @endphp
                    <label class="form-label">Approvers</label>
                    {!! Form::select('level[][approvers[]]', $admins->pluck('email', 'id'), $level->approvers->pluck('id')->toArray(),
                    ['class' => 'form-select select2User', 'multiple', 'data-placeholder' => 'Approvers'], $optionParameters) !!}
                  </div>
                  <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                    <button class="btn btn-label-danger mt-4" data-repeater-delete>
                      <i class="ti ti-x ti-xs me-1"></i>
                      <span class="align-middle">Delete</span>
                    </button>
                  </div>
                </div>
                <hr>
              </div>
          @empty
          @endforelse
          </div>
          <div class="d-flex justify-content-end">
            <div class="mb-0 mx-1">
              <button class="btn btn-primary" data-repeater-create>
                <i class="ti ti-plus me-1"></i>
                <span class="align-middle">Add</span>
              </button>
            </div>
            <div class="mb-0">
              <button class="btn btn-primary btn-next" data-form="ajax-form"> <span class="align-middle">Update</span></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection
@push('scripts')
@endpush
