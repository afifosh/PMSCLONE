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
      var data=$("#add-mail-account").serialize();
      $.ajax({
      url:'{{url('/admin/mail/accounts/connection')}}',
      method:'post',
      data:data,
      success:function(response){
        debugger;
        var folders=response.data;
        var html='';
        for(var i=0; i<folders.length; i++){
         html+=` <div class="form-check">
            <input name="folders[]" class="form-check-input" type="checkbox" value='`+JSON.stringify(folders[i])+`' id="folder-`+folders[i].remote_id+`">
            <label class="form-check-label" for="folder-`+folders[i].remote_id+`">
            `+folders[i].display_name+`
            </label>
          </div>`;
          if(folders[i].children){
            var child=folders[i].children;
            html+="<div style='padding-left:5px'>";
         for(var j=0; j<child.length; j++){
          html+=` <div class="form-check">
            <input name="folders[]" class="form-check-input" type="checkbox" value='`+JSON.stringify(child[j])+`' id="folder-`+child[j].remote_id+`">
            <label class="form-check-label" for="folder-`+child[j].remote_id+`">
            `+child[j].display_name+`
            </label>
          </div>`;
         }
         html+="</div>";
        }
        $("#test-connection").hide();
        $("#save-account").prop('disabled',false);
        }
        
          $('#folders-area').html(html);
      }
    })
    
    });
</script>
<script>
  function doAction(action){
        $('#send-email').attr("style","display:inline-block !important");
        $('#forward-email,#reply-email').attr("style","display:none !important");
  }
  @if($accounts->count()>0)
            $('#send-email').on('click', function() {
              var account_id=$("#select-account").find(":selected").val();
              var url="{{url('/admin/inbox/emails/:accountId')}}";
              url=url.replace(":accountId",account_id);
              var quill = new Quill('.email-editor');
              $("#message").val(quill.root.innerHTML);
          saveRecord(this,"POST",url,"email-compose-form","Please try again");
            });
            function forwardEmail(elem,messageId){
              var url="{{url('/admin/emails/:messageId/forward')}}";
              url=url.replace(":messageId",messageId);
              var quill = new Quill('.email-editor');
              $("#message").val(quill.root.innerHTML);
          saveRecord(elem,"POST",url,"email-compose-form","Please try again");
            }
            function replyEmail(elem,messageId){
              var url="{{url('/admin/emails/:messageId/reply')}}";
              url=url.replace(":messageId",messageId);
              var quill = new Quill('.email-editor');
              $("#message").val(quill.root.innerHTML);
          saveRecord(elem,"POST",url,"email-compose-form","Please try again");
            }
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