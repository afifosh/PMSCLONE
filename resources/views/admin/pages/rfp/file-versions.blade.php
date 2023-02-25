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
