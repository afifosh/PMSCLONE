
<div class="card shadow-none border-0 rounded-0 app-email-view-header p-3 py-md-3 py-2">
        <!-- Email View : Title  bar-->
        <div class="d-flex justify-content-between align-items-center py-2">
          <div class="d-flex align-items-center overflow-hidden">
            <i class="ti ti-chevron-left ti-sm cursor-pointer me-2" onclick="$('#app-email-view').removeClass('show');"></i>
            <h6 class="text-truncate mb-0 me-2">{{$message->subject}}</h6>
          </div>
          <!-- Email View : Action  bar-->
          <div class="d-flex">
          @if(auth()->user()->hasPermission(['Owner','Editor','Contributor'],$message->account))
            <div class="dropdown ms-3">
              <i class="ti ti-dots-vertical cursor-pointer" id="dropdownMoreOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              </i>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMoreOptions">
              @if($message->is_read)
               <form id="markUnread">
               <a class="dropdown-item" href="javascript:saveRecord(this,'POST','{{url('/admin/emails/'.$message->id.'/unread')}}','markUnread','Please try again');">
                  <i class="ti ti-mail ti-xs me-1"></i>
                  <span class="align-middle">Mark as unread</span>
                </a>
               </form>
@else
<form id="markRead">
               <a class="dropdown-item" href="javascript:saveRecord(this,'POST','{{url('/admin/emails/'.$message->id.'/read')}}','markRead','Please try again');">
                  <i class="ti ti-mail-opened ti-xs me-1"></i>
                  <span class="align-middle">Mark as unread</span>
                </a>
</form>
                @endif
              </div>
            </div>
            @endif

          </div>
        </div>
        <hr class="app-email-view-hr mx-n3 mb-2">
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
         @if(auth()->user()->hasPermission(['Owner','Editor'],$message->account))
          <i class='ti ti-trash cursor-pointer me-3' onclick="deleteRecord('delete', '{{url('admin/emails/'.$message->id.'')}}', 'Message deleted successfully.')"></i>
           @endif
         @if(auth()->user()->hasPermission(['Owner','Editor','Contributor'],$message->account))
            @if($message->is_read)
               <form id="markUnread2">
                  <i onclick="saveRecord(this,'POST','{{url('/admin/emails/'.$message->id.'/unread')}}','markUnread2','Please try again');" class="ti ti-mail cursor-pointer me-3"></i>
               </form>
        @else
      <form id="markRead2">
                  <i onclick="saveRecord(this,'POST','{{url('/admin/emails/'.$message->id.'/read')}}','markRead2','Please try again');" class="ti ti-mail-opened cursor-pointer me-3"></i>
      </form>
                @endif
                @endif

        
          </div>
        </div>
      </div>
      <hr class="m-0">

      <!-- Email View : Content-->
      <div id="view-email" class="app-email-view-content py-4" style="overflow-y:auto">
      @if(count($message->getThread())>0)
      <p onclick="$('.email-card-prev').toggle();$(this).toggle();" class="email-earlier-msgs text-center text-muted cursor-pointer mb-5">{{$message->getThread()->count()}} Earlier Message</p>
      @foreach($message->getThread() as $msg)
      <div class="card email-card-prev mx-sm-4 mx-3">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-sm-0 mb-3">
              <div alt="user-avatar" class="flex-shrink-0 rounded-circle me-3" >
              <svg width="40" height="40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 600 599.9" style="enable-background:new 0 0 600 599.9;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<g>
	<path class="st0" d="M350.3,281.1c-4.3,4.3-10.1,6.7-16.2,6.7c-6.1,0-11.9-2.4-16.2-6.7c-8.9-8.9-8.9-23.4,0-32.4   c4.3-4.3,10.1-6.7,16.2-6.7c6.1,0,11.9,2.4,16.2,6.7C359.2,257.6,359.2,272.1,350.3,281.1 M276.4,287.8   c-12.6,0-22.9-10.3-22.9-22.9s10.3-22.9,22.9-22.9c12.6,0,22.9,10.3,22.9,22.9S289,287.8,276.4,287.8 M142,361.2l-44-15.3   l-15.1,43.6l-62.7-21.7l21.7-62.7l44,15.3l15.1-43.6l62.7,21.7L142,361.2z M557.5,259l42.5-18.7V84.5   c-27.9,82.9-101.6,142.9-193.8,142.9h-6.7L577.5,0H325.2v94.5h48.7v132.9H254.6V0h-84.9l-34.3,53.1l-6.3-6.5   c-2.7-2.8-7.2-5.8-13.2-8.8c-6-3-11.2-4.8-15.4-5.3l-11.3-1.3L108.6,0H0v223c1.5,0.1,3.1,0.1,4.7,0.1c9.1,0,17.5-0.9,25-2.7   c7.4-1.8,13.3-3.9,17.7-6.4c4.4-2.5,8.2-5.3,11.1-8.1c3.6-3.5,5.2-5.7,5.9-7c1-1.9,1.2-2.7,1.2-2.7c0-2.4-1.1-8.5-8.2-21.2   C51.5,164.8,44,155,35,145.8l-3.2-3.3l20.4-54.5l7.9,9.9c7.2,9,15.2,16.2,23.8,21.5c6.5-8.7,13.4-17,20.3-24.5   c8.4-9,17.4-17.5,27-25.1c9.8-7.8,20.3-14,31.1-18.5c11-4.5,22.1-6.8,32.9-6.8c14.4,0,25.5,4,33.1,11.8   c7.6,7.8,11.4,18.7,11.4,32.4c0,7.8-2.7,18.6-8.2,33.1c-5.3,13.9-11,25.6-16.9,34.6c-10.4,16-22.1,27.6-34.6,34.3   c-12.5,6.7-29.4,10.1-50.1,10.1c-14.7,0-28.2-1.9-40.1-5.7c-0.9,4.1-2.2,10.1-4.2,19.2c-5.2,22.2-15.8,40.6-31.6,54.7   c-14.8,13.2-33,20.3-54.1,21.4v165.4l307.8-155.9l37.8,74.5L0,549.5v50.4h196.2c-22.8-21-47.1-53.7-52.5-86.6   c-0.2-1.2-0.6-5.2,2.6-6.6c3.7-1.6,6.4,1.7,7.3,2.8c13,15.9,38.3,41.3,78.7,60.1c14.6,6.3,30.6,9.7,46.4,9.7   c21.9,0,41.2-6.3,55.9-18.3c27.7-22.6,47.1-57.8,53-96.6c5.6-36.4-4.2-69.5-14.3-104.7c-3.6-12.6-13-43.7,15.3-54.2   c25.1-9.3,42.3,16.6,47.6,31.4c11.8,32.5,18.7,66.9,19.9,99.6c1.7,44.9-6.5,108.2-55,154.3c-3.4,3.2-7,6.2-10.7,9.1h112.9   l-24.3-271.2l97.4-20.6l-19.1,291.8H600V271.2l-42.5,18.7l-52.8-23.2l-47.1,20.7l-11.4-25.9l58.4-25.7L557.5,259z"></path>
	<path class="st0" d="M180,134.8c6.1-0.8,10.9-1.7,14.1-2.8c3-1,5.4-2.1,7.1-3.4c1.9-1.4,2.4-2.1,2.4-2.1c0.2-0.4,0.2-0.6,0.2-0.6   c0-7.2-2.1-13.2-6.5-18.2c-4-4.6-11.2-7-21.2-7c-11.3,0-24,3.1-37.5,9.1c-10.5,4.7-20.6,11.4-30.1,20.1c14,4,30.6,6,49.3,6   C166.3,136,173.7,135.6,180,134.8"></path>
</g>
</svg>  
            </div>
              <div class="flex-grow-1 ms-1">
                <h6 class="m-0">{{$msg->from->name}}</h6>
                <small class="text-muted">{{$msg->from->address}}</small>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <p class="mb-0 me-3 text-muted">{{$msg->date}}</p>
              @if(auth()->user()->hasPermission(['Owner','Editor','Contributor'],$msg->account))
              <div class="dropdown me-3">
                <i class="ti ti-dots-vertical cursor-pointer" id="dropdownEmail" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </i>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownEmail">
                  <a class="dropdown-item scroll-to-reply" href="javascript:reply({{$msg}})">
                    <i class="ti ti-corner-up-left me-1"></i>
                    <span class="align-middle">Reply</span>
                  </a>
                  <a class="dropdown-item" href="javascript:forward({{$msg}})">
                    <i class="ti ti-corner-up-right me-1"></i>
                    <span class="align-middle">Forward</span>
                  </a>
              
                </div>
              </div>
              @endif
            </div>
          </div>
          <div style="overflow-y: auto;" class="card-body">
          {!! $msg->preview_text !!}
        <input type="hidden" id="mail_type" value="compose"/>
      @if($msg->attachments->count()>0)
        <hr>
      <p class="email-attachment-title mb-2">Attachments</p>
        @foreach($msg->attachments as $attachment)
      <div class="cursor-pointer">
              <i class="ti ti-file"></i>
              <a href="{{$attachment->getDownloadUrl()}}" class="align-middle ms-1">{{$attachment->filename}}</a>
            </div>
            @endforeach
            @endif  
      </div>
        </div>
        @endforeach
@endif

   
   
      <div class="card @if(count($message->getThread())>0)email-card-last @endif mx-sm-4 mx-3 mt-4">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-sm-0 mb-3">
              <div alt="user-avatar" class="flex-shrink-0 rounded-circle me-3" >
              <svg width="40" height="40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 600 599.9" style="enable-background:new 0 0 600 599.9;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<g>
	<path class="st0" d="M350.3,281.1c-4.3,4.3-10.1,6.7-16.2,6.7c-6.1,0-11.9-2.4-16.2-6.7c-8.9-8.9-8.9-23.4,0-32.4   c4.3-4.3,10.1-6.7,16.2-6.7c6.1,0,11.9,2.4,16.2,6.7C359.2,257.6,359.2,272.1,350.3,281.1 M276.4,287.8   c-12.6,0-22.9-10.3-22.9-22.9s10.3-22.9,22.9-22.9c12.6,0,22.9,10.3,22.9,22.9S289,287.8,276.4,287.8 M142,361.2l-44-15.3   l-15.1,43.6l-62.7-21.7l21.7-62.7l44,15.3l15.1-43.6l62.7,21.7L142,361.2z M557.5,259l42.5-18.7V84.5   c-27.9,82.9-101.6,142.9-193.8,142.9h-6.7L577.5,0H325.2v94.5h48.7v132.9H254.6V0h-84.9l-34.3,53.1l-6.3-6.5   c-2.7-2.8-7.2-5.8-13.2-8.8c-6-3-11.2-4.8-15.4-5.3l-11.3-1.3L108.6,0H0v223c1.5,0.1,3.1,0.1,4.7,0.1c9.1,0,17.5-0.9,25-2.7   c7.4-1.8,13.3-3.9,17.7-6.4c4.4-2.5,8.2-5.3,11.1-8.1c3.6-3.5,5.2-5.7,5.9-7c1-1.9,1.2-2.7,1.2-2.7c0-2.4-1.1-8.5-8.2-21.2   C51.5,164.8,44,155,35,145.8l-3.2-3.3l20.4-54.5l7.9,9.9c7.2,9,15.2,16.2,23.8,21.5c6.5-8.7,13.4-17,20.3-24.5   c8.4-9,17.4-17.5,27-25.1c9.8-7.8,20.3-14,31.1-18.5c11-4.5,22.1-6.8,32.9-6.8c14.4,0,25.5,4,33.1,11.8   c7.6,7.8,11.4,18.7,11.4,32.4c0,7.8-2.7,18.6-8.2,33.1c-5.3,13.9-11,25.6-16.9,34.6c-10.4,16-22.1,27.6-34.6,34.3   c-12.5,6.7-29.4,10.1-50.1,10.1c-14.7,0-28.2-1.9-40.1-5.7c-0.9,4.1-2.2,10.1-4.2,19.2c-5.2,22.2-15.8,40.6-31.6,54.7   c-14.8,13.2-33,20.3-54.1,21.4v165.4l307.8-155.9l37.8,74.5L0,549.5v50.4h196.2c-22.8-21-47.1-53.7-52.5-86.6   c-0.2-1.2-0.6-5.2,2.6-6.6c3.7-1.6,6.4,1.7,7.3,2.8c13,15.9,38.3,41.3,78.7,60.1c14.6,6.3,30.6,9.7,46.4,9.7   c21.9,0,41.2-6.3,55.9-18.3c27.7-22.6,47.1-57.8,53-96.6c5.6-36.4-4.2-69.5-14.3-104.7c-3.6-12.6-13-43.7,15.3-54.2   c25.1-9.3,42.3,16.6,47.6,31.4c11.8,32.5,18.7,66.9,19.9,99.6c1.7,44.9-6.5,108.2-55,154.3c-3.4,3.2-7,6.2-10.7,9.1h112.9   l-24.3-271.2l97.4-20.6l-19.1,291.8H600V271.2l-42.5,18.7l-52.8-23.2l-47.1,20.7l-11.4-25.9l58.4-25.7L557.5,259z"></path>
	<path class="st0" d="M180,134.8c6.1-0.8,10.9-1.7,14.1-2.8c3-1,5.4-2.1,7.1-3.4c1.9-1.4,2.4-2.1,2.4-2.1c0.2-0.4,0.2-0.6,0.2-0.6   c0-7.2-2.1-13.2-6.5-18.2c-4-4.6-11.2-7-21.2-7c-11.3,0-24,3.1-37.5,9.1c-10.5,4.7-20.6,11.4-30.1,20.1c14,4,30.6,6,49.3,6   C166.3,136,173.7,135.6,180,134.8"></path>
</g>
</svg>  
            </div>
              <div class="flex-grow-1 ms-1">
                @if($message->folders->first()->type=='sent')
                <h6 class="m-0">To:</h6>
                <small class="text-muted">{{$message->to->first()->address}}</small>
                @else
                <h6 class="m-0">{{$message->from->name}}</h6>
                <small class="text-muted">{{$message->from->address}}</small>
                @endif
              </div>
            </div>
            <div class="d-flex align-items-center">
              <p class="mb-0 me-3 text-muted">{{$message->date}}</p>
              @if(auth()->user()->hasPermission(['Owner','Editor','Contributor'],$message->account))
              <div class="dropdown me-3">
                <i class="ti ti-dots-vertical cursor-pointer" id="dropdownEmail" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </i>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownEmail">
                  <a class="dropdown-item scroll-to-reply" href="javascript:reply({{$message}})">
                    <i class="ti ti-corner-up-left me-1"></i>
                    <span class="align-middle">Reply</span>
                  </a>
                  <a class="dropdown-item" href="javascript:forward({{$message}})">
                    <i class="ti ti-corner-up-right me-1"></i>
                    <span class="align-middle">Forward</span>
                  </a>
              
                </div>
              </div>
              @endif
            </div>
          </div>
          <div style="overflow-y: auto;" class="card-body">
          {!! $message->preview_text !!}
        <input type="hidden" id="mail_type" value="compose"/>
      @if($message->attachments->count()>0)
        <hr>
      <p class="email-attachment-title mb-2">Attachments</p>
        @foreach($message->attachments as $attachment)
      <div class="cursor-pointer">
              <i class="ti ti-file"></i>
              <a href="{{$attachment->getDownloadUrl()}}" class="align-middle ms-1">{{$attachment->filename}}</a>
            </div>
            @endforeach
            @endif  
      </div>
        </div>
        
      </div>
      <script>
        function reply(message){
          $('#reply-email').attr("style","display:inline-block !important");
        $('#forward-email,#send-email').attr("style","display:none !important");
        $('#reply-email').attr('data-id',message.id);
          $("#subject").val("RE: "+message.subject);
          if(message.reply_to!=undefined){
         $("#to").val((message.reply_to.length==0?message.from.address:message.reply_to[0].address));
          }
          else{
         $("#to").val(message.from.address);
          }
         var dateStringFormatted = new Date(message.date).toLocaleString('en-US', {weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true}).replace(/,/g, '');

         let wroteText = `On `+ dateStringFormatted + " "+(message.from.name==null?message.from.address:message.from.name)+ " <" + message.from.address + `> wrote:`;

      var html=" <br /><div class='syncmail_attr'>" +
        wroteText +
        '</div><blockquote class="syncmail_quote">';
          if(message.html_body==null || message.html_body==""){
           html+= message.text_body; 

          }
          else{
           html+= message.html_body; 

          }
        html+='</blockquote>';
         $('.email-editor').html(html);
         $('mail_type').val('reply');
         var quill = new Quill('.email-editor');

          $("#emailComposeSidebar").modal('show');
        }
        function forward(message){
        $('#forward-email').attr('data-id',message.id);

          $('#forward-email').attr("style","display:inline-block !important");
        $('#send-email,#reply-email').attr("style","display:none !important");
          $("#subject").val('FW: '+message.subject);
          var dateStringFormatted = new Date(message.date).toLocaleString('en-US', {weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true}).replace(/,/g, '');
          debugger;
         $('mail_type').val('forward');

          var html ="<br /><div class='syncmail_attr'>" +
          '--------Forwarded message--------</br>';
            if(message.from.name==null){
              html+='From: '+ message.from.name +' ';
            } 
            html+='&lt;'+message.from.address+'&gt;</br>'+
            'Date: '+dateStringFormatted +'</br>'+
            'Subject: '+ message.subject +'</br>'+
            `To: &lt;`+ message.to[0].address  +`&gt;</br>`+
          '</div>' +
          '<br /><div>';
          if(message.html_body==null || message.html_body==""){
           html+= message.text_body; 
          }
          else{
           html+= message.html_body; 
          } 
          html+='</div>'

          $('.email-editor').html(html);
         
         var quill = new Quill('.email-editor');

          $("#emailComposeSidebar").modal('show');

        }
   
      </script>