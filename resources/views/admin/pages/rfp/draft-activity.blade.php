@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Files Activity')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
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
@include('admin.pages.rfp.header', ['tab' => 'files-activity'])

<div class="card">
  <div class="card-body pb-0">
    <ul>
      @forelse ($audits as $audit)
      <li>
          @lang('article.updated.metadata', $audit->getMetadata())

          @foreach ($audit->getModified() as $attribute => $modified)
          <ul>
              <li>@lang('article.'.$audit->event.'.modified.'.$attribute, $modified)</li>
          </ul>
          @endforeach
      </li>
      @empty
      <p>@lang('article.unavailable_audits')</p>
      @endforelse
  </ul>

  </div>
  <div class="row">
    <div class="col-12">
      <div class="card-footer d-flex justify-content-end">
        {{$audits->links()}}
      </div>
    </div>
  </div>
  {{-- {{$logs->links()}} --}}
</div>



@endsection

