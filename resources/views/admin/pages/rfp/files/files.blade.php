@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Files')


@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end float-right">
                                <button type="button"
                                    class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2 add-file-modal-btn"><i
                                        class="mdi mdi-plus me-1"></i> Upload New File</button>
                            </div>
                        </div><!-- end col-->
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;" class="align-middle">
                                        <div class="form-check font-size-16">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th class="align-middle">File Id</th>
                                    <th class="align-middle">File</th>
                                    <th class="align-middle">Shared By</th>
                                    <th class="align-middle">Uploaded At</th>
                                    <th class="align-middle">Shared At</th>
                                    <th class="align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->files as $file)
                                    <tr>
                                        <td>
                                            <div class="form-check font-size-16">
                                                <input class="form-check-input" type="checkbox" id="orderidcheck01">
                                                <label class="form-check-label" for="orderidcheck01"></label>
                                            </div>
                                        </td>
                                        <td><a href="javascript: void(0);"
                                                class="text-body fw-bold">{{ $file->file->file_id }}</a> </td>
                                        <td>{{ $file->file->file_name }}</td>
                                        <td>{{ $file->file->uploader->email }}</td>
                                        <td>
                                            {{ $file->file->created_at->diffForHumans() }}
                                        </td>
                                        <td>
                                            {{ $file->created_at->diffForHumans() }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-3">
                                                <a href="{{ route('edit-file', $file->file) }}" class="text-success"><i data-feather='edit' class="font-large-1"></i></a>
                                                <a href="javascript:void(0);" class="text-danger"><i
                                                        class="mdi mdi-delete font-size-18"></i></a>
                                                @if ($file->file->user_id == auth()->id())
                                                    <button onclick="open_share_modal('{{ $file->file->id }}')"
                                                        class="text-danger btn btn-warning">share</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <ul class="pagination pagination-rounded justify-content-end mb-2">
                        <li class="page-item disabled">
                            <a class="page-link" href="javascript: void(0);" aria-label="Previous">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="javascript: void(0);">1</a></li>
                        <li class="page-item"><a class="page-link" href="javascript: void(0);">2</a></li>
                        <li class="page-item"><a class="page-link" href="javascript: void(0);">3</a></li>
                        <li class="page-item"><a class="page-link" href="javascript: void(0);">4</a></li>
                        <li class="page-item"><a class="page-link" href="javascript: void(0);">5</a></li>
                        <li class="page-item">
                            <a class="page-link" href="javascript: void(0);" aria-label="Next">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Modal -->
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal fade text-left add-file-modal" tabindex="-1" role="dialog" aria-labelledby="add-file-modalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-primary" id="add-file-modalLabel">Upload File</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="formFile" class="form-label">Select File</label>
                                        <input class="form-control" type="file" name="file" id="formFile"
                                            required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Share</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade text-left share-file-modal" tabindex="-1" role="dialog" aria-labelledby="share-file-modalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <form action="{{ route('share_file') }}" method="POST">
                        @csrf
                        <input type="hidden" id="file_id_val" name="file_id" value="">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-primary" id="share-file-modalLabel">Share File</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="users">Share With</label>
                                    <select class="multiple-select2 form-control" name="users[]" multiple="multiple"
                                        required>
                                        @forelse ($data->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->email }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Share</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade info-modal" tabindex="-1" role="dialog" aria-labelledby="info-modalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="info-modalLabel">File Info</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <p> <b> Last Modified By : </b> <span id="lmb"></span></p>
                            <p> <b> Last Modified At : </b> <span id="lma"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal -->
    @endsection
    @section('vendor-script')
        <!-- vendor files -->
        <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    @endsection
    @section('page-script')
        {{-- <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script> --}}
        <script>
            $(function() {
                $(".multiple-select2").select2({
                    dropdownParent: $(".share-file-modal"),
                    width: 465
                });
                $('.add-file-modal-btn').click(function(e) {
                    e.preventDefault();
                    $('.add-file-modal').modal('show');
                });
            });

            function open_share_modal(file_id) {
                $('#file_id_val').val(file_id);
                $('.share-file-modal').modal('show');
            }

            function open_details_modal(file_id) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('files.get_item_details') }}",
                    data: {
                        file_id: file_id
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        console.log(res);
                        $('#lmb').text(res.lastModifiedBy.user.displayName);
                        $('#lma').text(res.lastModifiedDateTime);
                        $('.info-modal').modal('show');
                    }
                });
            }
        </script>
    @endsection
