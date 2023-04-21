@extends('admin.layouts/layoutMaster')

@section('title', 'Emails')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-email.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}" />
<style>
  .selected-image-tag{
    background-color: #f2f2f2;
    padding: 5px;
    border-radius: 5px;
    margin:3px;
  }
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
<script src="{{asset('assets/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
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
  @if($accounts->filter(function ($account) {
    return auth()->user()->hasPermission(['Owner', 'Reviewer', 'Editor', 'Contributor'], $account);
})->count()>0)
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
          var isSync=false;
          for(var i=0; i<folders.length; i++){
            if(folders[i].syncable){
              isSync=true;
            html+=`<li class="d-flex justify-content-between folder-items" onclick="populateMessages(`+account_id+`,`+folders[i].id+`,1);$('#app-email-view').removeClass('show');" id="folder-`+folders[i].id+`">`;
           if(folders[i].parent_id!=null){
            html+='<div style="padding-left:5px">';
           }
            html+=`<a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">`+folders[i].display_name+`</span>
            </a>`+(folders[i].unread_count!=0?'<div class="badge bg-label-primary rounded-pill badge-center">'+folders[i].unread_count+'</div>':"")+``;
            if(folders[i].parent_id!=null){
            html+='</div>';
           }
            html+=`</li>`;
        }
        if(!isSync){
          $("#email-list-area").hide();
          $("#no-folders-area").show();
        }
        else{
          $("#email-list-area").show();
          $("#no-folders-area").hide();          
        }

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
    $("#mail-search").attr('data-account',account_id);
    $("#mail-search").attr('data-folder',folder_id);
    $(".folder-items").removeClass('active');
    $("#folder-"+folder_id).addClass('active');
    $.ajax({
        url: url,
        type: "GET",
        success: function (response, status) {
          var messages=response.data;
           var html='';
           if(messages.length==0){
            html+="<li class='d-flex align-items-center'>No email available for this folder.</li>";
           }
           else{
           for(var i=0; i<messages.length; i++){
          html+=`
            <li  class="email-list-item `+(messages[i].is_read? 'email-marked-read"':'')+`" data-12="true" data-bs-toggle="sidebar" onclick="showMessage(`+folder_id+`,`+messages[i].id+`);" data-id="#`+messages[i].id+`">
              <div class="d-flex align-items-center">
                <div class="form-check mb-0">
                  <input class="email-list-item-input form-check-input" data-id="`+messages[i].id+`" type="checkbox" id="email-`+messages[i].id+`">
                  <label class="form-check-label" for="email-`+messages[i].id+`"></label>
                </div>
                <div class="email-list-item-content ms-2 ms-sm-0 me-2">
                  <span class="email-list-item-username me-2">`+(messages[i].from.name==null?messages[i].from.address:messages[i].from.name)+`</span>
                  <span class="email-list-item-subject d-xl-inline-block d-block"> `+messages[i].subject+`</span>
                </div>
                <div class="email-list-item-meta ms-auto d-flex align-items-center" >`;
                if(messages[i].attachments.length>0){
                  html+=`<span class="email-list-item-attachment ti ti-paperclip ti-xs cursor-pointer me-2 float-end float-sm-none"></span>`
                }
                const messageDate = moment(messages[i].date);
const today = moment().startOf('day');
const yesterday = moment().subtract(1, 'day').startOf('day');

let displayDate;
if (messageDate.isSame(today, 'd')) {
  // If the message date is the same as today, show only the time
  displayDate = messageDate.format('h:mm A');
} else if (messageDate.isSame(yesterday, 'd')) {
  // If the message date is yesterday, show 'Yesterday'
  displayDate = 'Yesterday';
} else {
  // Otherwise, show the full date and time
  displayDate = messageDate.format('MMMM D, YYYY h:mm A');
} 
                html+= `<small class="email-list-item-time text-muted" style="width: 170px; whitespace:nowrap; display:inline-block !important">`+displayDate+`</small>
                </div>
              </div>
            </li>`;

          }
        }
           $('#email-list').html(html);
           $('#records-counter').html((response.from==null?0:response.from)+ "-"+ (response.to==null?0:response.to) +" of "+ response.total);
           if(page>1)
           $('#prev-page').attr("href", "javascript:populateMessages("+account_id+","+folder_id+","+(page-1)+")");
          if(page<response.last_page)
           $('#next-page').attr("href", "javascript:populateMessages("+account_id+","+folder_id+","+(page+1)+")");
           $('.email-list-item-input').click(function(e) {
    e.stopPropagation();
});
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
  function searchMails(elem){
    var page=1;
    var term=$(elem).val();
    var account_id=$(elem).data('account');
    var folder_id=$(elem).data('folder');
    var url='{{url("/admin/inbox/emails/:accountId/:folderId")}}?term='+term;
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
                  <input class="email-list-item-input form-check-input" type="checkbox" data-id="`+messages[i].id+`" id="email-`+messages[i].id+`">
                  <label class="form-check-label" for="email-`+messages[i].id+`"></label>
                </div>
                <div class="email-list-item-content ms-2 ms-sm-0 me-2">
                  <span class="h6 email-list-item-username me-2">`+(messages[i].from.name==null?messages[i].from.address:messages[i].from.name)+`</span>
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
           $('#records-counter').html((response.from==null?0:response.from)+ "-"+ (response.to==null?0:response.to) +" of "+ response.total);
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
  function bulkAction(action){
    var elements=$('.email-list-item-input:checked');
    if(elements.length==0){
      alert('No item selected');
      return;
    }
    var ids=elements.map(function (idx, ele) {
    return $(ele).data('id');
    }).get();
    if(action=='unread'){
      $.ajax({
        url: "{{url('admin/emails/bulkUnread')}}",
        type: "GET",
        data: {id:ids},
        success: function (response, status) {
          toastr.success(response);
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    })  
    }
    if(action=='delete'){
      Swal.fire({
        title: "Are you sure?",
        text: "Are you sure you want to delete these messages?",
        type: "warning",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        confirmButtonClass: "btn btn-primary",
        buttonsStyling: !1
    }).then(
      $.ajax({
        url: "{{url('admin/emails/bulkDelete')}}",
        type: "GET",
        data: {id:ids},
        success: function (response, status) {
          toastr.success(response);
        },
        error: function (response) {
            var message = "";
            if
                (response.responseJSON.message == undefined) { message = errorMesage }
            else { message = response.responseJSON.message }
            toastr.error(message);
        }
    })
    )
  }
  }
</script>
<script>
const fileInput = document.getElementById('file-input');
const selectedImagesContainer = document.getElementById('selected-images');

fileInput.addEventListener('change', (event) => {
  // Remove all existing tags and cross signs from the container
  selectedImagesContainer.innerHTML = '';

  // Get the selected files from the input field
  const files = event.target.files;

  // Loop through the selected files
  for (const file of files) {
    // Create a new tag element
    const tag = document.createElement('span');
    tag.innerText = file.name;
    tag.classList.add('selected-image-tag');

    // Create a new cross sign element
    const crossSign = document.createElement('i');
    crossSign.classList.add('ti', 'ti-trash', 'cursor-pointer', 'ms-2');
    crossSign.addEventListener('click', () => {
      // Remove the tag from the container
      selectedImagesContainer.removeChild(tag);

      // Remove the corresponding file from the input field
      const newFiles = Array.from(fileInput.files).filter((f) => f !== file);
      const dataTransfer = new DataTransfer();
      newFiles.forEach((f) => {
        dataTransfer.items.add(f);
      });
      fileInput.files = dataTransfer.files;
    });

    // Add the tag and cross sign to the container
    tag.appendChild(crossSign);

    selectedImagesContainer.appendChild(tag);
  }
});

</script>
<!-- <script>
  var input = $('.tags');
input.each(function(){
  new Tagify(this);
})
</script> -->
@endsection

@section('content')
@if($accounts->filter(function ($account) {
    return auth()->user()->hasPermission(['Owner', 'Reviewer', 'Editor', 'Contributor'], $account);
})->count()>0)
<div class="app-email card">
  <div class="row g-0">
    <!-- Email Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" style="width:auto;overflow-y: auto;" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <div class="mb-3">
        <select id="select-account" class="select2 form-control" style="width:100%">
          @foreach($accounts as $account)
          @if(auth()->user()->hasPermission(['Owner','Reviewer','Editor','Contributor'],$account))
          <option @if($account->isprimary()) selected @endif value="{{$account->id}}"> {{$account->email}}</option>
          @endif
          @endforeach
        </select>
        </div>
        @if(auth()->user()->hasPermission(['Owner','Editor','Contributor'],$account))
        <button class="btn btn-primary btn-compose" onclick="doAction('compose');" data-bs-toggle="modal" data-bs-target="#emailComposeSidebar">Compose</button>
        @endif
      </div>
      <!-- Email Filters -->
      <div class="email-filters py-2" style="height:auto">
        <!-- Email Filters: Folder -->
        <ul id="folders" class="email-filter-folders list-unstyled mb-4" >
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
    <div id="email-list-area" style="display:none;" class="col app-emails-list">
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
                  <input type="text" onkeyup="searchMails(this);" id="mail-search" class="form-control email-search-input border-0" placeholder="Search mail" aria-label="Search mail" aria-describedby="email-search">
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
          @if(auth()->user()->hasPermission(['Owner','Editor'],$account))
              <i class="ti ti-trash email-list-delete cursor-pointer me-2" onclick="bulkAction('delete');"></i>
          @endif
              <i class="ti ti-mail-opened email-list-read cursor-pointer me-2" onclick="bulkAction('unread');"></i>
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
        <div style="overflow-y: auto !important;" class="email-list pt-0">
        <ul id="email-list"  class="list-unstyled m-0">

        </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Emails List -->

    <div id="no-folders-area" style="display:none; margin:auto" class="col app-emails-list">
    <div class="text-center mb-3">
      <div class="card-body">
        <i class="ti ti-xl ti-folder" style="font-size:3rem !important"></i>
        <p class="card-text">This account has no active folders. Enable active folders by editing the mail account, the active folders will be the folders that will be synchronized to the application.</p>
        <a href="javascript:editAccount();" id="activate-folders" class="btn btn-primary">Activate Folders</a>
        <a href="{{url('/admin/mail/accounts/manage-accounts')}}" id="manage-accounts" class="btn btn-secondary">Manage Accounts</a>
      </div>
    </div>
  </div>

    <!-- Email View -->
    <div class="col app-email-view flex-grow-0 bg-body" id="app-email-view">
      
    </div>
    <!-- Email View -->
  </div>

  <!-- Compose Email -->
  <div class="app-email-compose modal" data-bs-backdrop="static" id="emailComposeSidebar" tabindex="-1" aria-labelledby="emailComposeSidebarLabel" aria-hidden="true">
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
              <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2 tags" id="to" name="to" placeholder="someone@email.com">
               
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
                <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2 tags" name="cc" id="cc" placeholder="someone@email.com">
              </div>
            </div>
            <div class="email-compose-bcc d-none">
              <hr class="container-m-nx my-2">
              <div class="d-flex align-items-center">
                <label for="bcc" class="form-label mb-0">Bcc: </label>
                <input type="text" class="form-control border-0 shadow-none flex-grow-1 mx-2 tags" id="bcc" name="bcc" placeholder="someone@email.com">
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
                <div class="">
                  <button type="button" id="send-email" class="btn btn-primary"><i class="ti ti-send ti-xs me-1"></i>Send</button>
                  <button type="button" id="reply-email" onclick="replyEmail(this,$(this).data('id'));" class="btn btn-primary " style="display:none !important"><i class="ti ti-send ti-xs me-1"></i>Reply</button>
                  <button type="button" id="forward-email" onclick="forwardEmail(this,$(this).data('id'));" class="btn btn-primary " style="display:none !important"><i class="ti ti-send ti-xs me-1"></i>Forward</button>
                
                </div>
                <label for="file-input"><i class="ti ti-paperclip cursor-pointer ms-2"></i></label>
                <input type="file" name="attachments[]" multiple class="d-none" id="file-input">
                <div id="selected-images"></div>
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
  <div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6 mt-4">
      <h4 class="text-center">No email accounts configured</h4>
      <p class="mb-3 text-center"> Connect an account to start sending and organize emails in order close deals faster </p>
      <div class="row">
        <div class="col-md-6">
        <ul class="p-0 m-0">
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" style="width: 20px;" class="w-20 text-primary-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">2-way email sync with your email provider. </p>
              
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-6 w-6 text-primary-600" style="width: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">2-way email sync with your email provider. </p>
              
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class=" bg-label-danger" style="width: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" style="">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">

Associate emails to many Contacts, Companies and Deals.
</p>
              
            </div>
          </li>
          
          
          
        </ul>
        </div>
        <div class="col-md-6">
        <ul class="p-0 m-0">
          
          
          
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-primary rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-6 w-6 text-primary-600" style="width: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">Save time by making use of predefined templates. </p>
              
            </div>
          </li>
          <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-6 w-6 text-primary-600" style="width: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">

Add customized signature for a more professional look.
</p>
              
            </div>
          </li>
          <li class="d-flex justify-content-between align-items-center">
            <div class="badge bg-label-danger rounded p-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-6 w-6 text-primary-600" style="width: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
              </svg></div>
            <div class="d-flex justify-content-between w-100 flex-wrap">
              <p class="mb-0 ms-3">

Connect via IMAP, your Gmail or Outlook account.
</p>
              
            </div>
          </li>
        </ul>
        </div>
       
      </div>
      <div class="d-flex justify-content-center flex-wrap gap-4 mt-2">
        <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','shared');" class="btn btn-primary" data-toggle="ajax-modal">Connect Shared Account</button>
        <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" onclick="localStorage.setItem('acc_type','personal');" class="btn btn-primary">Connect Personal Account</button>
      </div>
    </div>
  </div>









</div>

@endif
@include('admin.pages.emails.partials.connect-account')
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-xxl offcanvas-end scrollable-container" data-bs-backdrop="static" tabindex="-1" id="edit-account-modal" style="overflow-y:auto;width:50%; background-color:white !important" aria-labelledby="editAccountModal">
<div></div>
</div>
@endsection
