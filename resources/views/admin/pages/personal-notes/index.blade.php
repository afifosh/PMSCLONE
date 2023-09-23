@extends('admin/layouts/layoutMaster')

@section('title', 'Private Notes')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-private-notes.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/admin-private-notes.js')}}"></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
<div class="app-email">
  <div class="row g-0">
    <!-- Task Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Note" data-href="{{route('admin.private-notes.create')}}">Add Note</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Tags</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">All</span>
            </a>
            {{-- <div class="badge bg-label-success rounded-pill badge-center">{{$project->stages->count()}}</div> --}}
          </li>
          @forelse ($tags as $tag)
            <li class="d-flex justify-content-between" data-target="{{slug($tag->name)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="align-middle ms-2">{{$tag->name}}</span>
              </a>
              {{-- <div class="badge bg-label-warning rounded-pill badge-center">{{$notes->whereHas('status', $status)->count()}}</div> --}}
            </li>
          @empty
          @endforelse
        </ul>
      </div>
    </div>
    <!--/ Task Sidebar -->

    <!-- Task List -->
    <div class="col app-emails-list">
      <div class="shadow-none border-0">
        <div class="emails-list-header p-3 py-lg-3 py-2">
        <!-- Task List: Items -->
        <div class="row">
          @forelse ($notes as $note)
          <div class="card col-4 m-2">
            <div class="card-body">
              <p class="fw-bold">{{$note->title}}</p>
              <p class="">{{\Illuminate\Support\Str::limit($note->description, 100, '...')}}</p>
            </div>
            <div class="card-footer">
              <div class="d-flex justify-content-end">
                <i class="me-2 fa-solid cursor-pointer fa-pen" data-href="{{route('admin.private-notes.edit', [$note])}}" data-toggle="ajax-modal" data-title="Edit Note"></i>
                <i class="fa-solid fa-trash cursor-pointer" data-href="{{route('admin.private-notes.destroy', [$note])}}" data-toggle="ajax-delete"></i>
              </div>
            </div>
          </div>
          @empty
          @endforelse
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Task List -->
  </div>
</div>
@endsection
