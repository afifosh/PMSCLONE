<script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
<script>
    $('#connection_type').on('change',function(){
        if($(this).find(':selected').val()==='Imap'){
            $("#imap-area").css("filter","none");
            $('#save-account').prop('disabled',true);        
            $('#test-connection').show();        
        }
        else{
            $("#imap-area").css("filter","blur(4px)");
            $('#test-connection').attr('style', 'display: none !important');        
            $('#save-account').prop('disabled',false);        

        }
    });
    $('#save-account').on('click',function(){
      if($('#connection_type').find(':selected').val()==='Gmail'){
       var url="{{url('/admin/mail/accounts/:accType/google/connect')}}?period="+$('input[name=initial_sync_from]:checked').val();
       url=url.replace(':accType',localStorage.getItem('acc_type'));
        window.location=(url);
        }
        else if($('#connection_type').find(':selected').val()==='Outlook'){
            var url="{{url('/admin/mail/accounts/:accType/microsoft/connect')}}?period="+$('input[name=initial_sync_from]:checked').val();
            url=url.replace(':accType',localStorage.getItem('acc_type'));
            window.location=(url);

        }
        else if($('#connection_type').find(':selected').val()==='Imap'){
          saveRecord(this,"POST","{{url('/admin/mail/accounts')}}","add-mail-account","Please try again");

        }
    });

    $("#test-connection").on('click',function(){
     var data= saveRecord(this,"POST","{{url('/admin/mail/accounts/connection')}}","add-mail-account","Please try again");
      console.log(data.folders);
      var html=` <div class="form-check">
            <input name="validate_cert" class="form-check-input" type="checkbox" value="0" id="validate_cert">
            <label class="form-check-label" for="validate_cert">
            Allow non secure certificate.
            </label>
          </div>`;
          $('#folders-area').html(html);
    });
</script>
<script>
  @if($accounts->count()>0)
            $('#send-email').on('click', function() {
              var account_id=$("#select-account").find(":selected").val();
              var url="{{url('/admin/inbox/emails/:accountId')}}";
              url=url.replace(":accountId",account_id);
          saveRecord(this,"POST",url,"email-compose-form","Please try again");
            });
            @endif
            function editAccount(){
              var account_id=$("#select-account").find(":selected").val();
              var url="{{url('/admin/mail/accounts/:accountId/edit')}}";
              url=url.replace(":accountId",account_id);
              ajaxCanvas(url,"edit-account-modal");          
            }
  function sync(){
    var account_id=$("#select-account").find(":selected").val();
    var url="{{url('/admin/mail/accounts/:accountId/sync')}}";
    url=url.replace(':accountId',account_id);
    $.ajax({
      url:url,
      method:'GET',
      success:function(response){
        location.reload();
      }
    })
    
  }
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
            html+=`<li class="d-flex justify-content-between folder-items" onclick="populateMessages(`+account_id+`,`+folders[i].id+`)" id="folder-`+folders[i].id+`">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">`+folders[i].display_name+`</span>
            </a>
          </li>`;

          }
          $('#folders').html(html);
          populateMessages(account_id,folders[0].id);
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
  function populateMessages(account_id,folder_id){
    var url='{{url("/admin/inbox/emails/:accountId/:folderId}")}}';
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
                  <ul class="list-inline email-list-item-actions text-nowrap">
                    <li class="list-inline-item email-read"> <i class='ti ti-mail-opened'></i> </li>
                    <li class="list-inline-item email-delete"> <i class='ti ti-trash'></i></li>
                    <li class="list-inline-item"> <i class="ti ti-archive"></i> </li>
                  </ul>
                </div>
              </div>
            </li>`;

          }
           $('#email-list').html(html);
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