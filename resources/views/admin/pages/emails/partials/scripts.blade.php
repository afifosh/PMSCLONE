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
          $("#errors").hide();
          saveRecord(this,"POST","{{url('/admin/mail/accounts')}}","add-mail-account","Please try again");

        }
    });

    $("#test-connection").on('click',function(){
      $("#errors").hide();
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
      },
      error : function(jqXHR, textStatus, errorThrown) {
       var form=document.getElementById("add-mail-account");
            onerror(jqXHR,textStatus,errorThrown,form);
        },
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
        toastr.success(response);
      }
    })
    
  }
 

</script>