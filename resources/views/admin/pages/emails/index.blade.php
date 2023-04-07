@extends('admin.layouts/layoutMaster')

@section('title', 'Emails')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-email.css')}}" />
<style>
  #email-editor{
    max-height: 300px;
    overflow: auto;
  }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/js/app-email.js')}}"></script>
<script src="{{asset('assets/js/helper.js')}}"></script>
@endsection

@section('page-script')
@include('admin.pages.emails.partials.scripts')
<script>
   $('#select-account').on('change',function(){
    var account_id=$("#select-account").find(":selected").val();
    populateFolders(account_id);
  });
  @if($accounts->count()>0)
  $(function(){
    var account_id=$("#select-account").find(":selected").val();
    populateFolders(account_id);
    
  });
  @endif
  function populateFolders(account_id){
    var url='{{url("/admin/mail/accounts/:accountId")}}';
    url=url.replace(':accountId',account_id);
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
          var folders=response.folders;
          var html='';
          for(var i=0; i<folders.length; i++){
            html+=`<li class="d-flex justify-content-between folder-items" onclick="populateMessages(`+account_id+`,`+folders[i].id+`,1)" id="folder-`+folders[i].id+`">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">`+folders[i].display_name+`</span>
            </a>
          </li>`;

          }
          $('#folders').html(html);
          populateMessages(account_id,folders[0].id,1);
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });
  }
  function populateMessages(account_id,folder_id,page){
    var url='{{url("/admin/inbox/emails/:accountId/:folderId?page=")}}'+page;
    url=url.replace(':accountId',account_id);
    url=url.replace(':folderId',folder_id);
    $(".folder-items").removeClass('active');
    $("#folder-"+folder_id).addClass('active');
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
          var messages=response.data;
           var html='';
           for(var i=0; i<messages.length; i++){
          html+=`
            <li class="email-list-item" data-12="true" data-bs-toggle="sidebar" onclick="showMessage(`+folder_id+`,`+messages[i].id+`);" data-id="#`+messages[i].id+`">
              <div class="d-flex align-items-center">
                <div class="form-check mb-0">
                  <input class="email-list-item-input form-check-input" type="checkbox" id="email-`+messages[i].id+`">
                  <label class="form-check-label" for="email-`+messages[i].id+`"></label>
                </div>
                <div class="email-list-item-content ms-2 ms-sm-0 me-2">
                  <span class="h6 email-list-item-username me-2">`+messages[i].from.name+`</span>
                  <span class="email-list-item-subject d-xl-inline-block d-block"> `+messages[i].subject+`</span>
                </div>
                <div class="email-list-item-meta ms-auto d-flex align-items-center">
                  <span class="email-list-item-label badge badge-dot bg-danger d-none d-md-inline-block me-2" data-label="private"></span>
                  <small class="email-list-item-time text-muted">`+new Date(messages[i].date).toLocaleTimeString()+`</small>
                </div>
              </div>
            </li>`;

          }
           $('#email-list').html(html);
           $('#records-counter').html(response.from+ "-"+ response.to +" of "+ response.total);
           if(page>1)
           $('#prev-page').attr("href", "javascript:populateMessages("+account_id+","+folder_id+","+(page-1)+")");
          if(page<response.last_page)
           $('#next-page').attr("href", "javascript:populateMessages("+account_id+","+folder_id+","+(page+1)+")");
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });
  }

  function showMessage(folder_id,message_id){
    var url='{{url("/admin/inbox/emails/folders/:folderId/:messageId}")}}';
    url=url.replace(':folderId',folder_id);
    url=url.replace(':messageId',message_id);

    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
           $('#app-email-view').html(response);
           $('#app-email-view').addClass("show");
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    });
  }
</script>
@endsection

@section('content')
@if($accounts->count()>0)
<div class="app-email card">
  <div class="row g-0">
    <!-- Email Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" style="width:auto" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <div class="mb-3">
        <select id="select-account" class="select2 form-control" style="width:100%">
          @foreach($accounts as $account)
          <option value="{{$account->id}}"> {{$account->email}}</option>
          @endforeach
        </select>
        </div>
        <button class="btn btn-primary btn-compose" onclick="doAction('compose');" data-bs-toggle="modal" data-bs-target="#emailComposeSidebar">Compose</button>
      </div>
      <!-- Email Filters -->
      <div class="email-filters py-2">
        <!-- Email Filters: Folder -->
        <ul id="folders" class="email-filter-folders list-unstyled mb-4">
        <li class="d-flex justify-content-between">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">        Loading...
</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <!--/ Email Sidebar -->

    <!-- Emails List -->
    <div class="col app-emails-list">
      <div class="shadow-none border-0">
        <div class="emails-list-header p-3 py-lg-3 py-2">
          <!-- Email List: Search -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center w-100">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3" data-bs-toggle="sidebar" data-target="#app-email-sidebar" data-overlay></i>
              <div class="mb-0 mb-lg-2 w-100">
                <div class="input-group input-group-merge shadow-none">
                  <span class="input-group-text border-0 ps-0" id="email-search">
                    <i class="ti ti-search"></i>
                  </span>
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search mail" aria-label="Search mail" aria-describedby="email-search">
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-0 mb-md-2">
              <i onclick="sync();" class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1"></i>
              <div class="dropdown">
                <i class="ti ti-dots-vertical cursor-pointer" id="emailsActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </i>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="emailsActions">
                  <button class="dropdown-item" onclick="editAccount();">Edit Email Account</button>
                  <a class="dropdown-item" href="{{url('/admin/mail/accounts/manage-accounts')}}">Manage Accounts</a>
                  <button class="dropdown-item" data-bs-toggle="offcanvas" onclick="localStorage.setItem('acc_type','personal');" data-bs-target="#offcanvasAddUser" >Connect Personal Account</button>
                </div>
              </div>
            </div>
          </div>
          <hr class="mx-n3 emails-list-header-hr">
          <!-- Email List: Actions -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div class="form-check mb-0 me-2">
                <input class="form-check-input" type="checkbox" id="email-select-all">
                <label class="form-check-label" for="email-select-all"></label>
              </div>
              <i class="ti ti-trash email-list-delete cursor-pointer me-2"></i>
              <i class="ti ti-mail-opened email-list-read cursor-pointer me-2"></i>
              <div class="dropdown me-2">
                <i class="ti ti-folder cursor-pointer" id="dropdownMenuFolder" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuFolder">
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="ti ti-info-circle ti-xs me-1"></i>
                    <span class="align-middle">Spam</span>
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="ti ti-file ti-xs me-1"></i>
                    <span class="align-middle">Draft</span>
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="ti ti-trash ti-xs me-1"></i>
                    <span class="align-middle">Trash</span>
                  </a>
                </div>
              </div>
              <div class="dropdown">
                <i class="ti ti-tag cursor-pointer" id="dropdownLabel" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </i>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownLabel">
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="badge badge-dot bg-success me-1"></i>
                    <span class="align-middle">Workshop</span>
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="badge badge-dot bg-primary me-1"></i>
                    <span class="align-middle">Company</span>
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="badge badge-dot bg-info me-1"></i>
                    <span class="align-middle">Important</span>
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    <i class="badge badge-dot bg-danger me-1"></i>
                    <span class="align-middle">Private</span>
                  </a>
                </div>
              </div>
            </div>
            <div class="email-pagination d-sm-flex d-none align-items-center flex-wrap justify-content-between justify-sm-content-end">
              <span class="d-sm-block d-none mx-3 text-muted" id="records-counter"></span>
              <a class="email-prev ti ti-chevron-left scaleX-n1-rtl cursor-pointer text-muted me-2" id="prev-page"></a>
              <a class="email-next ti ti-chevron-right scaleX-n1-rtl cursor-pointer" id="next-page"></a>
            </div>
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Email List: Items -->
        <div  class="email-list pt-0">
        <ul id="email-list" class="list-unstyled m-0">

        </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Emails List -->

    <!-- Email View -->
    <div class="col app-email-view flex-grow-0 bg-body" id="app-email-view">
      
    </div>
    <!-- Email View -->
  </div>

  <!-- Compose Email -->
  <div class="app-email-compose modal" id="emailComposeSidebar" tabindex="-1" aria-labelledby="emailComposeSidebarLabel" aria-hidden="true">
    <div class="modal-dialog m-0 me-md-4 mb-4 modal-lg">
      <div class="modal-content p-0">
        <div class="modal-header py-3 bg-body">
          <h5 class="modal-title fs-5">Compose Mail</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body flex-grow-1 pb-sm-0 p-4 py-2">
          <form class="email-compose-form" id="email-compose-form" enctype="multipart/form-data">
          @csrf  
          <div class="email-compose-to d-flex justify-content-between align-items-center">
              <label class="form-label mb-0" for="emailContacts">To:</label>
              <div class="select2-primary border-0 shadow-none flex-grow-1 mx-2">
              <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2" id="to" name="to" placeholder="someone@email.com">
               
              </div>
              <div class="email-compose-toggle-wrapper">
                <a class="email-compose-toggle-cc" href="javascript:void(0);">Cc |</a>
                <a class="email-compose-toggle-bcc" href="javascript:void(0);">Bcc</a>
              </div>
            </div>

            <div class="email-compose-cc d-none">
              <hr class="container-m-nx my-2">
              <div class="d-flex align-items-center">
                <label for="cc" class="form-label mb-0">Cc: </label>
                <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2" name="cc" id="cc" placeholder="someone@email.com">
              </div>
            </div>
            <div class="email-compose-bcc d-none">
              <hr class="container-m-nx my-2">
              <div class="d-flex align-items-center">
                <label for="bcc" class="form-label mb-0">Bcc: </label>
                <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2" id="bcc" name="bcc" placeholder="someone@email.com">
              </div>
            </div>
            <hr class="container-m-nx my-2">
            <div class="email-compose-subject d-flex align-items-center mb-2">
              <label for="email-subject" class="form-label mb-0">Subject:</label>
              <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2" id="subject" name="subject" placeholder="Project Details">
            </div>
            <div class="email-compose-message container-m-nx">
              <div class="d-flex justify-content-end">
                <div class="email-editor-toolbar border-bottom-0 w-100">
                  <span class="ql-formats me-0">
                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>
                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>
                    <button class="ql-link"></button>
                    <button class="ql-image"></button>
                  </span>
                </div>
              </div>
              <div id="email-editor" class="email-editor"></div>
              <input type="hidden" name="message" id="message"/>
            </div>
            <hr class="container-m-nx mt-0 mb-2">
            <div class="email-compose-actions d-flex justify-content-between align-items-center mt-3 mb-3">
              <div class="d-flex align-items-center">
                <div class="btn-group">
                  <button type="button" id="send-email" class="btn btn-primary"><i class="ti ti-send ti-xs me-1"></i>Send</button>
                  <button type="button" id="reply-email" onclick="replyEmail(this,$(this).data('id'));" class="btn btn-primary " style="display:none !important"><i class="ti ti-send ti-xs me-1"></i>Reply</button>
                  <button type="button" id="forward-email" onclick="forwardEmail(this,$(this).data('id'));" class="btn btn-primary " style="display:none !important"><i class="ti ti-send ti-xs me-1"></i>Forward</button>
                
                </div>
                <label for="media"><i class="ti ti-paperclip cursor-pointer ms-2"></i></label>
                <input type="file" name="file" class="d-none" id="media">
              </div>
              <div class="d-flex align-items-center">
                <div class="dropdown">
                  <i class="ti ti-dots-vertical cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false"></i>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMoreActions">
                    <li><button type="button" class="dropdown-item">Add Label</button></li>
                    <li><button type="button" class="dropdown-item">Plain text mode</button></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><button type="button" class="dropdown-item">Print</button></li>
                    <li><button type="button" class="dropdown-item">Check Spelling</button></li>
                  </ul>
                </div>
                <button type="reset" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-trash"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- /Compose Email -->
</div>
@else
<div class="col-md-12 mb-4">
    <div class="card h-100 col-md-12">
      <div class="card-body row">
        <div class="col-md-6">
        <ul class="p-0 m-0">
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-success rounded p-2"><i class="ti ti-mail ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Emails</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">12,346</p>
                <p class="ms-3 text-success mb-0">0.3%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-info rounded p-2"><i class="ti ti-link ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Opened</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">8,734</p>
                <p class="ms-3 text-success mb-0">2.1%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-warning rounded p-2"><i class="ti ti-click ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Clicked</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">967</p>
                <p class="ms-3 text-success mb-0">1.4%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-primary rounded p-2"><i class="ti ti-users ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Subscribe</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">345</p>
                <p class="ms-3 text-success mb-0">8.5k</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-secondary rounded p-2"><i class="ti ti-alert-triangle ti-sm text-body"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Complaints</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">10</p>
                <p class="ms-3 text-success mb-0">1.5%</p>
              </div>
            </div>
          </li>
          <li class="d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><i class="ti ti-ban ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Unsubscribe</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">86</p>
                <p class="ms-3 text-success mb-0">0.8%</p>
              </div>
            </div>
          </li>
        </ul>
        </div>
        <div class="col-md-6">
        <ul class="p-0 m-0">
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-success rounded p-2"><i class="ti ti-mail ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Emails</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">12,346</p>
                <p class="ms-3 text-success mb-0">0.3%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-info rounded p-2"><i class="ti ti-link ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Opened</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">8,734</p>
                <p class="ms-3 text-success mb-0">2.1%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-warning rounded p-2"><i class="ti ti-click ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Clicked</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">967</p>
                <p class="ms-3 text-success mb-0">1.4%</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-primary rounded p-2"><i class="ti ti-users ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Subscribe</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">345</p>
                <p class="ms-3 text-success mb-0">8.5k</p>
              </div>
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-secondary rounded p-2"><i class="ti ti-alert-triangle ti-sm text-body"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Complaints</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">10</p>
                <p class="ms-3 text-success mb-0">1.5%</p>
              </div>
            </div>
          </li>
          <li class="d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><i class="ti ti-ban ti-sm"></i></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <h6 class="mb-0 ms-3">Unsubscribe</h6>
              <div class="d-flex">
                <p class="mb-0 fw-semibold">86</p>
                <p class="ms-3 text-success mb-0">0.8%</p>
              </div>
            </div>
          </li>
        </ul>
        </div>
       
      </div>
      <div class="card-footer">
      <div style="text-align:center">

            <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','shared');" class="btn btn-primary" data-toggle="ajax-modal">Connect Shared Account</button>
            <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','personal');" class="btn btn-primary">Connect Personal Account</button>
        </div>
      </div>
    </div>
  </div>

</div>

@endif
@include('admin.pages.emails.partials.connect-account')
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-xxl offcanvas-end" tabindex="-1" id="edit-account-modal" style="width:50%; background-color:white !important" aria-labelledby="editAccountModal">
<div></div>
</div>
@endsection
