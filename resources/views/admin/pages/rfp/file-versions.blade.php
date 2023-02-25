@php
    $configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Files Activity')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
    <script src={{ asset('assets/js/custom/ajax.js') }}></script>
    <script src={{ asset('assets/js/custom/select2.js') }}></script>
    <script src={{ asset('assets/js/custom/flatpickr.js') }}></script>
    <script>
        $(document).ready(function() {
            initFlatPickr();
        });
    </script>
@endsection

@section('content')
  <div class="row breadcrumbs-top">
    <div class="col-12 d-flex justify-content-between">
      <div class="d-flex">
        <span class="content-header-title float-left border-end px-2 me-2 h4">File Versions</span>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb pt-1">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">Home</a>
                </li>
                @if($draft_rfp)
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.index') }}">Draft RFPs</a>
                  </li>
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.show', $file->rfp_id) }}">{{ $file->rfp->name }}</a>
                  </li>
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.files.index', ['draft_rfp' => $file->rfp_id]) }}">Files</a>
                  </li>
                @else
                  <li class="breadcrumb-item">
                    <a href="{{ route('admin.draft-rfps.index') }}">Shared Files</a>
                  </li>
                @endif
                <li class="breadcrumb-item active">{{ $file->title }}</li>
            </ol>
        </nav>
      </div>
      <div>
        <div class="btn-group">
          <button class="btn btn-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.shared-files.file-activity', ['file' => $file->id, 'rfp' => $draft_rfp])}}">File Activity</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
      <div class="card-body">
          <table class="table">
              <thead>
                  <tr>
                      <th>Version</th>
                      <th>Size</th>
                      <th>Last Modified</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                  @php
                      $ver_count = @getFileVersion(getHistoryDir(getStoragePath(ltrim($file->curFilePath(), '/')))) ?? 0;
                  @endphp
                  @for ($ver_count; $ver_count > 0; $ver_count--)
                      <tr>
                          <td>Version {{ $ver_count }}</td>
                          <td>{{ human_filesize(Storage::size($file->curFilePath())) }}</td>
                          <td>{{ formatUNIXTimeStamp(Storage::lastModified($file->curFilePath())) }}</td>
                          <td><a
                                  href="{{ route('admin.draft-rfps.files.download', ['draft_rfp' => $file->rfp_id, 'file' => $file, 'version' => $ver_count]) }}">
                                  <i class="fa fa-xl fa-download"></i>
                              </a></td>
                      </tr>
                  @endfor
              </tbody>
          </table>
      </div>
  </div>
@endsection
