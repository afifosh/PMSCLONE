@extends('admin/layouts/layoutMaster')

{{-- page title --}}
@section('title','File Manager')

@section('vendor-style')
  <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors/fonts/font-awesome/css/font-awesome.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/jstree.min.css')}}">     
  
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}">
  
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css')}}">   
    <!-- END: Vendor CSS-->
@endsection
{{-- page styles --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/ext-component-tree.css')}}">        
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-file-manager.css')}}">    
@endsection


<!--@section('page-style')-->
<!--  <link rel="stylesheet" href="{{asset('assets/css/file-manager/app-file-manager.css')}}">-->
<!--  <link rel="stylesheet" href="{{asset('assets/css/file-manager/ext-component-tree.css')}}">-->
<!--  <link rel="stylesheet" href="{{asset('assets/css/file-manager/jstree.min.css')}}">-->
<!--@endsection-->
@section('vendor-script')
<script src="{{asset('assets/js/file-manager/app-file-manager.js')}}"></script>
<script src="{{asset('assets/js/file-manager/jstree.min.js')}}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
 <script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script>
    
        //  Notifications & messages scrollable
    $('.scrollable-container').each(function () {
      var scrollable_container = new PerfectScrollbar($(this)[0], {
        wheelPropagation: false
      });
    });
    
</script>
  

@endsection

@section('page-script')
<script>
  feather.replace()
</script>
@endsection
@section('content')

<!-- overlay container -->
<div class="body-content-overlay"></div>

<!-- file manager app content starts -->
<div class="app-content content file-manager-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">

            <div class="sidebar-left">
                <div class="sidebar">
@include('admin/pages/rfp//file-manager-sidebar')
                </div>
            </div>
       
            <div class="content-right">
                <div class="content-wrapper container-xxl p-0">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <!-- overlay container -->
                        <div class="body-content-overlay"></div>

                        <!-- file manager app content starts -->
                        <div class="file-manager-main-content">
                            <!-- search area start -->
                            <div class="file-manager-content-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="sidebar-toggle d-block d-xl-none float-start align-middle ms-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu font-medium-5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                                    </div>
                                    <div class="input-group input-group-merge shadow-none m-0 flex-grow-1">
                                        <span class="input-group-text border-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        </span>
                                        <input type="text" class="form-control files-filter border-0 bg-transparent" placeholder="Search">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="file-actions">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down-circle font-medium-2 cursor-pointer d-sm-inline-block d-none me-50"><circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 12"></polyline><line x1="12" y1="8" x2="12" y2="16"></line></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash font-medium-2 cursor-pointer d-sm-inline-block d-none me-50"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle font-medium-2 cursor-pointer d-sm-inline-block d-none" data-bs-toggle="modal" data-bs-target="#app-file-manager-info-sidebar"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        <div class="dropdown d-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-medium-2 cursor-pointer" role="button" id="fileActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="fileActions">
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-move cursor-pointer me-50"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                                                    <span class="align-middle">Open with</span>
                                                </a>
                                                <a class="dropdown-item d-sm-none d-block" href="#" data-bs-toggle="modal" data-bs-target="#app-file-manager-info-sidebar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle cursor-pointer me-50"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <span class="align-middle">More Options</span>
                                                </a>
                                                <a class="dropdown-item d-sm-none d-block" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash cursor-pointer me-50"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    <span class="align-middle">Delete</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus cursor-pointer me-50"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                                    <span class="align-middle">Add shortcut</span>
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder-plus cursor-pointer me-50"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                                                    <span class="align-middle">Move to</span>
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star cursor-pointer me-50"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                    <span class="align-middle">Add to starred</span>
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet cursor-pointer me-50"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                                                    <span class="align-middle">Change color</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download cursor-pointer me-50"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    <span class="align-middle">Download</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group view-toggle ms-50" role="group">
                                        <input type="radio" class="btn-check" name="view-btn-radio" data-view="grid" id="gridView" checked="" autocomplete="off">
                                        <label class="btn btn-outline-primary p-50 btn-sm waves-effect" for="gridView">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                        </label>
                                        <input type="radio" class="btn-check" name="view-btn-radio" data-view="list" id="listView" autocomplete="off">
                                        <label class="btn btn-outline-primary p-50 btn-sm waves-effect" for="listView">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- search area ends here -->

                            <div class="file-manager-content-body ps ps--active-y">
                                <!-- drives area starts-->
                                <div class="drives">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="files-section-title mb-75">Drives</h6>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                                            <div class="card shadow-none border cursor-pointer">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <img src="../../../app-assets/images/icons/drive.png" alt="google drive" height="38">
                                                        <div class="dropdown-items-wrapper">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical" id="dropdownMenuLink1" role="button" data-bs-toggle="dropdown" aria-expanded="false"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink1">
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw me-25"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                    <span class="align-middle">Refresh</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings me-25"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                                                    <span class="align-middle">Manage</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash me-25"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                                    <span class="align-middle">Delete</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-1">
                                                        <h5>Google drive</h5>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-50">
                                                        <span class="text-truncate">35GB Used</span>
                                                        <small class="text-muted">50GB</small>
                                                    </div>
                                                    <div class="progress progress-bar-warning progress-md mb-0" style="height: 10px">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="70" aria-valuemax="100" style="width: 70%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                                            <div class="card shadow-none border cursor-pointer">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <img src="../../../app-assets/images/icons/dropbox.png" alt="dropbox" height="38">
                                                        <div class="dropdown-items-wrapper">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical" id="dropdownMenuLink2" role="button" data-bs-toggle="dropdown" aria-expanded="false"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink2">
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw me-25"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                    <span class="align-middle">Refresh</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings me-25"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                                                    <span class="align-middle">Manage</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash me-25"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                                    <span class="align-middle">Delete</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-1">
                                                        <h5>Dropbox</h5>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-50">
                                                        <span class="text-truncate">1.2GB Used</span>
                                                        <small class="text-muted">2GB</small>
                                                    </div>
                                                    <div class="progress progress-bar-success progress-md mb-0" style="height: 10px">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="70" aria-valuemax="100" style="width: 68%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                                            <div class="card shadow-none border cursor-pointer">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <img src="../../../app-assets/images/icons/onedrivenew.png" alt="icloud" height="38" class="p-25">
                                                        <div class="dropdown-items-wrapper">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical" id="dropdownMenuLink3" role="button" data-bs-toggle="dropdown" aria-expanded="false"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink3">
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw me-25"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                    <span class="align-middle">Refresh</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings me-25"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                                                    <span class="align-middle">Manage</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash me-25"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                                    <span class="align-middle">Delete</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-1">
                                                        <h5>OneDrive</h5>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-50">
                                                        <span class="text-truncate">1.6GB Used</span>
                                                        <small class="text-muted">2GB</small>
                                                    </div>
                                                    <div class="progress progress-bar-primary progress-md mb-0" style="height: 10px">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="70" aria-valuemax="100" style="width: 80%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                                            <div class="card shadow-none border cursor-pointer">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <img src="../../../app-assets/images/icons/icloud-1.png" alt="icloud" height="38" class="p-25">
                                                        <div class="dropdown-items-wrapper">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical" id="dropdownMenuLink4" role="button" data-bs-toggle="dropdown" aria-expanded="false"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink4">
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw me-25"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                    <span class="align-middle">Refresh</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings me-25"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                                                    <span class="align-middle">Manage</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash me-25"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                                    <span class="align-middle">Delete</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-1">
                                                        <h5>iCloud</h5>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-50">
                                                        <span class="text-truncate">1.8GB Used</span>
                                                        <small class="text-muted">3GB</small>
                                                    </div>
                                                    <div class="progress progress-bar-info progress-md mb-0" style="height: 10px">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="70" aria-valuemax="100" style="width: 60%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- drives area ends-->

                                <!-- Folders Container Starts -->
                                <div class="view-container">
                                    <h6 class="files-section-title mt-25 mb-75">Folders</h6>
                                    
                                    <div class="files-header">
                                        <h6 class="fw-bold mb-0">Filename</h6>
                                        <div>
                                            <h6 class="fw-bold file-item-size d-inline-block mb-0">Size</h6>
                                            <h6 class="fw-bold file-last-modified d-inline-block mb-0">Last modified</h6>
                                            <h6 class="fw-bold d-inline-block me-1 mb-0">Actions</h6>
                                        </div>
                                    </div>
                                    <div class="card file-manager-item folder level-up">
                                        <div class="card-img-top file-logo-wrapper">
                                            <div class="d-flex align-items-center justify-content-center w-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                                            </div>
                                        </div>
                                        <div class="card-body ps-2 pt-0 pb-1">
                                            <div class="content-wrapper">
                                                <p class="card-text file-name mb-0">...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card file-manager-item folder">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck1">
                                            <label class="form-check-label" for="customCheck1"></label>
                                        </div>
                                        <div class="card-img-top file-logo-wrapper">
                                            <div class="dropdown float-end">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical toggle-dropdown mt-n25"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center w-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="content-wrapper">
                                                <p class="card-text file-name mb-0">Projects</p>
                                                <p class="card-text file-size mb-0">2gb</p>
                                                <p class="card-text file-date">01 may 2019</p>
                                            </div>
                                            <small class="file-accessed text-muted">Last accessed: 21 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="card file-manager-item folder">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck2">
                                            <label class="form-check-label" for="customCheck2"></label>
                                        </div>
                                        <div class="card-img-top file-logo-wrapper">
                                            <div class="dropdown float-end">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical toggle-dropdown mt-n25"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center w-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="content-wrapper">
                                                <p class="card-text file-name mb-0">Design</p>
                                                <p class="card-text file-size mb-0">500mb</p>
                                                <p class="card-text file-date">05 may 2019</p>
                                            </div>
                                            <small class="file-accessed text-muted">Last accessed: 18 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="d-none flex-grow-1 align-items-center no-result mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle me-50"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        No Results
                                    </div>
                                </div>
                                <!-- /Folders Container Ends -->

                                <!-- Files Container Starts -->
                                <div class="view-container">
                                    <h6 class="files-section-title mt-2 mb-75">Files</h6>
                               @forelse ($draft_rfp->files as $file)
                                <div class="card file-manager-item file">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck7" />
                                      <label class="form-check-label" for="customCheck7"></label>
                                  </div>
                                  <div class="card-img-top file-logo-wrapper">
                                      <div class="dropdown float-end">
                                          <i data-feather="more-vertical" class="toggle-dropdown mt-n25"></i>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-center w-100">
                                          <img src="../../../app-assets/images/icons/jpg.png" alt="file-icon" height="35" />
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="content-wrapper">
                                          <p class="card-text file-name mb-0"><a href="{{route('admin.edit-file', $file->id)}}">{{$file->title}}</a></p>
                                          <p class="card-text file-size mb-0">{{human_filesize(Storage::size($file->file))}}</p>
                                          {{-- <p class="card-text file-date">23 may 2019</p> --}}
                                      </div>
                                      <small class="file-accessed text-muted">Last modified: {{formatUNIXTimeStamp(Storage::lastModified($file->file))}}</small>
                                  </div>
                                </div>
                              @empty
                                <div class="flex-grow-1 align-items-center no-result mb-3">
                                  <i data-feather="alert-circle" class="me-50"></i>
                                  No Results
                                </div>
                              @endforelse
                              @forelse ($draft_rfp->files as $file)
                                <div class="card file-manager-item file">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck7" />
                                      <label class="form-check-label" for="customCheck7"></label>
                                  </div>
                                  <div class="card-img-top file-logo-wrapper">
                                      <div class="dropdown float-end">
                                          <i data-feather="more-vertical" class="toggle-dropdown mt-n25"></i>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-center w-100">
                                          <img src="../../../app-assets/images/icons/jpg.png" alt="file-icon" height="35" />
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="content-wrapper">
                                          <p class="card-text file-name mb-0"><a href="{{route('admin.edit-file', $file->id)}}">{{$file->title}}</a></p>
                                          <p class="card-text file-size mb-0">{{human_filesize(Storage::size($file->file))}}</p>
                                          {{-- <p class="card-text file-date">23 may 2019</p> --}}
                                      </div>
                                      <small class="file-accessed text-muted">Last modified: {{formatUNIXTimeStamp(Storage::lastModified($file->file))}}</small>
                                  </div>
                                </div>
                              @empty
                                <div class="flex-grow-1 align-items-center no-result mb-3">
                                  <i data-feather="alert-circle" class="me-50"></i>
                                  No Results
                                </div>
                              @endforelse              
                              @forelse ($draft_rfp->files as $file)
                                <div class="card file-manager-item file">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck7" />
                                      <label class="form-check-label" for="customCheck7"></label>
                                  </div>
                                  <div class="card-img-top file-logo-wrapper">
                                      <div class="dropdown float-end">
                                          <i data-feather="more-vertical" class="toggle-dropdown mt-n25"></i>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-center w-100">
                                          <img src="../../../app-assets/images/icons/jpg.png" alt="file-icon" height="35" />
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="content-wrapper">
                                          <p class="card-text file-name mb-0"><a href="{{route('admin.edit-file', $file->id)}}">{{$file->title}}</a></p>
                                          <p class="card-text file-size mb-0">{{human_filesize(Storage::size($file->file))}}</p>
                                          {{-- <p class="card-text file-date">23 may 2019</p> --}}
                                      </div>
                                      <small class="file-accessed text-muted">Last modified: {{formatUNIXTimeStamp(Storage::lastModified($file->file))}}</small>
                                  </div>
                                </div>
                              @empty
                                <div class="flex-grow-1 align-items-center no-result mb-3">
                                  <i data-feather="alert-circle" class="me-50"></i>
                                  No Results
                                </div>
                              @endforelse
                              @forelse ($draft_rfp->files as $file)
                                <div class="card file-manager-item file">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck7" />
                                      <label class="form-check-label" for="customCheck7"></label>
                                  </div>
                                  <div class="card-img-top file-logo-wrapper">
                                      <div class="dropdown float-end">
                                          <i data-feather="more-vertical" class="toggle-dropdown mt-n25"></i>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-center w-100">
                                          <img src="../../../app-assets/images/icons/jpg.png" alt="file-icon" height="35" />
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="content-wrapper">
                                          <p class="card-text file-name mb-0"><a href="{{route('admin.edit-file', $file->id)}}">{{$file->title}}</a></p>
                                          <p class="card-text file-size mb-0">{{human_filesize(Storage::size($file->file))}}</p>
                                          {{-- <p class="card-text file-date">23 may 2019</p> --}}
                                      </div>
                                      <small class="file-accessed text-muted">Last modified: {{formatUNIXTimeStamp(Storage::lastModified($file->file))}}</small>
                                  </div>
                                </div>
                              @empty
                                <div class="flex-grow-1 align-items-center no-result mb-3">
                                  <i data-feather="alert-circle" class="me-50"></i>
                                  No Results
                                </div>
                              @endforelse                              
                              @forelse ($draft_rfp->files as $file)
                                <div class="card file-manager-item file">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck7" />
                                      <label class="form-check-label" for="customCheck7"></label>
                                  </div>
                                  <div class="card-img-top file-logo-wrapper">
                                      <div class="dropdown float-end">
                                          <i data-feather="more-vertical" class="toggle-dropdown mt-n25"></i>
                                      </div>
                                      <div class="d-flex align-items-center justify-content-center w-100">
                                          <img src="../../../app-assets/images/icons/jpg.png" alt="file-icon" height="35" />
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="content-wrapper">
                                          <p class="card-text file-name mb-0"><a href="{{route('admin.edit-file', $file->id)}}">{{$file->title}}</a></p>
                                          <p class="card-text file-size mb-0">{{human_filesize(Storage::size($file->file))}}</p>
                                          {{-- <p class="card-text file-date">23 may 2019</p> --}}
                                      </div>
                                      <small class="file-accessed text-muted">Last modified: {{formatUNIXTimeStamp(Storage::lastModified($file->file))}}</small>
                                  </div>
                                </div>
                              @empty
                                <div class="flex-grow-1 align-items-center no-result mb-3">
                                  <i data-feather="alert-circle" class="me-50"></i>
                                  No Results
                                </div>
                              @endforelse
                                </div>
                                <!-- /Files Container Ends -->
                            <div class="ps__rail-x" style="left: 0px; bottom: -8px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 8px; height: 754px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 6px; height: 634px;"></div></div></div>
                        </div>
                        <!-- file manager app content ends -->

                        <!-- File Info Sidebar Starts-->
                        <div class="modal modal-slide-in fade show" id="app-file-manager-info-sidebar">
                            <div class="modal-dialog sidebar-lg">
                                <div class="modal-content p-0">
                                    <div class="modal-header d-flex align-items-center justify-content-between mb-1 p-2">
                                        <h5 class="modal-title">menu.js</h5>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash cursor-pointer me-50" data-bs-dismiss="modal"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x cursor-pointer" data-bs-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </div>
                                    </div>
                                    <div class="modal-body flex-grow-1 pb-sm-0 pb-1">
                                        <ul class="nav nav-tabs tabs-line" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#details-tab" role="tab" aria-controls="details-tab" aria-selected="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                                    <span class="align-middle ms-25">Details</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#activity-tab" role="tab" aria-controls="activity-tab" aria-selected="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                                    <span class="align-middle ms-25">Activity</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="details-tab" role="tabpanel" aria-labelledby="details-tab">
                                                <div class="d-flex flex-column justify-content-center align-items-center py-5">
                                                    <img src="../../../app-assets/images/icons/js.png" alt="file-icon" height="64">
                                                    <p class="mb-0 mt-1">54kb</p>
                                                </div>
                                                <h6 class="file-manager-title my-2">Settings</h6>
                                                <ul class="list-unstyled">
                                                    <li class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>File Sharing</span>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" id="sharing">
                                                            <label class="form-check-label" for="sharing"></label>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>Synchronization</span>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" checked="" id="sync">
                                                            <label class="form-check-label" for="sync"></label>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>Backup</span>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" id="backup">
                                                            <label class="form-check-label" for="backup"></label>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <hr class="my-2">
                                                <h6 class="file-manager-title my-2">Info</h6>
                                                <ul class="list-unstyled">
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Type</p>
                                                        <p class="fw-bold">JS</p>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Size</p>
                                                        <p class="fw-bold">54kb</p>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Location</p>
                                                        <p class="fw-bold">Files &gt; Documents</p>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Owner</p>
                                                        <p class="fw-bold">Sheldon Cooper</p>
                                                    </li>
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Modified</p>
                                                        <p class="fw-bold">12th Aug, 2020</p>
                                                    </li>

                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <p>Created</p>
                                                        <p class="fw-bold">01 Oct, 2019</p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane fade" id="activity-tab" role="tabpanel" aria-labelledby="activity-tab">
                                                <h6 class="file-manager-title my-2">Today</h6>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar avatar-sm me-50">
                                                        <img src="../../../app-assets/images/avatars/5-small.png" alt="avatar" width="28">
                                                    </div>
                                                    <div class="more-info">
                                                        <p class="mb-0">
                                                            <span class="fw-bold">Mae</span>
                                                            shared the file with
                                                            <span class="fw-bold">Howard</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-light-primary me-50">
                                                        <span class="avatar-content">SC</span>
                                                    </div>
                                                    <div class="more-info">
                                                        <p class="mb-0">
                                                            <span class="fw-bold">Sheldon</span>
                                                            updated the file
                                                        </p>
                                                    </div>
                                                </div>
                                                <h6 class="file-manager-title mt-3 mb-2">Yesterday</h6>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar avatar-sm bg-light-success me-50">
                                                        <span class="avatar-content">LH</span>
                                                    </div>
                                                    <div class="more-info">
                                                        <p class="mb-0">
                                                            <span class="fw-bold">Leonard</span>
                                                            renamed this file to
                                                            <span class="fw-bold">menu.js</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-50">
                                                        <img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="Avatar" width="28">
                                                    </div>
                                                    <div class="more-info">
                                                        <p class="mb-0">
                                                            <span class="fw-bold">You</span>
                                                            shared this file with Leonard
                                                        </p>
                                                    </div>
                                                </div>
                                                <h6 class="file-manager-title mt-3 mb-2">3 days ago</h6>
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar avatar-sm me-50">
                                                        <img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="Avatar" width="28">
                                                    </div>
                                                    <div class="more-info">
                                                        <p class="mb-50">
                                                            <span class="fw-bold">You</span>
                                                            uploaded this file
                                                        </p>
                                                        <img src="../../../app-assets/images/icons/js.png" alt="Avatar" class="me-50" height="24">
                                                        <span class="fw-bold">app.js</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- File Info Sidebar Ends -->

                        <!-- File Dropdown Starts-->
                        <div class="dropdown-menu dropdown-menu-end file-dropdown">
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye align-middle me-50"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span class="align-middle">Preview</span>
                            </a>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus align-middle me-50"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                <span class="align-middle">Share</span>
                            </a>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy align-middle me-50"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span class="align-middle">Make a copy</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit align-middle me-50"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                <span class="align-middle">Rename</span>
                            </a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#app-file-manager-info-sidebar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info align-middle me-50"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                <span class="align-middle">Info</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle me-50"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                <span class="align-middle">Delete</span>
                            </a>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle align-middle me-50"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span class="align-middle">Report</span>
                            </a>
                        </div>
                        <!-- /File Dropdown Ends -->

                        <!-- Create New Folder Modal Starts-->
                        <div class="modal fade" id="new-folder-modal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">New Folder</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" class="form-control" value="New folder" placeholder="Untitled folder">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary me-1 waves-effect waves-float waves-light" data-bs-dismiss="modal">Create</button>
                                        <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Create New Folder Modal Ends -->

                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- file manager app content ends -->

{{-- modals --}}
  <div class="modal fade" id="add-file-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
              <form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                    <div class="modal-header">
                      <h5 class="modal-title" id="globalModalTitle">Upload File</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                      <div class="modal-body">
                          <div class="form-group">
                              <label for="formFile" class="form-label">Select File</label>
                              <input class="form-control" type="file" name="file" id="formFile"
                                  required>
                          </div>
                          <input type="hidden" name="Draft_RFP" value="{{$draft_rfp->id}}">
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" data-form="ajax-form" class="btn btn-primary">Upload</button>
                      </div>
              </form>
          </div>
      </div>
    </div>
@endsection
