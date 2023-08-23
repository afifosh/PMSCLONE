<script id="tmplConversation" type="text/x-jsrender">
    <div class="chat-header">

    <div class="chat-history-header border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex overflow-hidden align-items-center">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-contacts"></i>
              <div class="flex-shrink-0 avatar chat__area-icon">
                <img src="{{:user.photo_url}}" alt="<?php __('chat::messages.person_image') ?>" class="rounded-circle open-profile-menu" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-sidebar-right">
              </div>
              <div class="chat-contact-info flex-grow-1 ms-2">
                {{if user.project_id}}
                <h6 class="m-0"><a href="/admin/projects/{{:user.project_id}}" class="text-decoration-none text-dark">{{>user.project.name}}</a></h6>
                {{else}}
                    <h6 class="m-0">{{>user.name}}</h6>
                {{/if}}   
              </div>
            </div>
            <div class="d-flex align-items-center">
              <i class="ti ti-phone-call cursor-pointer d-sm-block d-none me-3"></i>
              <i class="ti ti-video cursor-pointer d-sm-block d-none me-3"></i>
              <i class="ti ti-search cursor-pointer d-sm-block d-none me-3"></i>
              <div class="dropdown d-flex align-self-center">
                <button class="btn p-0" type="button" id="chat-header-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chat-header-actions">
                   <a class="dropdown-item msg-delete-icon" href="#">Delete Message</a>
                   <a class="dropdown-item msg-delete-for-everyone" href="#">Delete For Everyone</a>
                   <a class="dropdown-item msg-replay" href="#" data-self-reply="1" data-message-id="{{:randomMsgId}}" data-message='{{:message}}' data-message-type='{{:randomMsgId}}'>Reply</a>
                  <a class="dropdown-item open-msg-info" data-message-id="{{:randomMsgId}}" data-is_group="{{:is_group}}">Info</a>
                </div>
              </div>
            </div>
          </div>
        </div>    
        <div class="chat-history-body bg-body ps">
          <ul class="list-unstyled chat-history chat-conversation" id="conversation-container">
            <li class="chat-message chat-message-right">
              <div class="d-flex overflow-hidden">
                <div class="chat-message-wrapper flex-grow-1">
                  <div class="chat-message-text">
                    <p class="mb-0">How can we help? We're here for you! 😄</p>
                  </div>
                  <div class="text-end text-muted mt-1">
                    <i class="ti ti-checks ti-xs me-1 text-success"></i>
                    <small>10:00 AM</small>
                  </div>
                </div>
                <div class="user-avatar flex-shrink-0 ms-3">
                  <div class="avatar avatar-sm">
                    <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
                  </div>
                </div>
              </div>
            </li>  
          </ul>
        </div>  
        <div class="chat__area-header position-relative">
            <div class="d-flex justify-content-between align-items-center flex-1 mx-3 chat__header-top">
                <input type="hidden" id="toId" value="{{:user.id}}">
                <input type="hidden" id="chatType" value="{{:user.id}}">
            </div>
        </div>
        <div class="chat__search-wrapper d-none">
            <div class="chat__search clearfix chat__search--responsive">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="<?php echo trans('chat::messages.search') ?>" class="chat__search-input"
                        id="searchMessageInput">
                <i class="fa-solid fa-magnifying-glass d-lg-none chat__search-responsive-icon"></i>
            </div>
        </div>
        <div class="loading-message chat__area-header-loader d-none">
            <svg width="150px" height="75px" viewBox="0 0 187.3 93.7"
            preserveAspectRatio="xMidYMid meet">
            <path stroke="#00c6ff" id="outline" fill="none" stroke-width="5" stroke-linecap="round"
            stroke-linejoin="round" stroke-miterlimit="10"
            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 -8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>
            <path id="outline-bg" opacity="0.05" fill="none" stroke="#f5981c" stroke-width="5"
            stroke-linecap="round"
            stroke-linejoin="round" stroke-miterlimit="10"
            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 -8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>
            </svg>
        </div>
        <!-- <div class="chat-conversation" id="conversation-container"></div> -->
    </div>



</script>
