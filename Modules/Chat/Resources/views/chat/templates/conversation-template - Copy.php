<script id="tmplConversation" type="text/x-jsrender">
    <div class="chat-header">

    <div class="chat-history-header border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex overflow-hidden align-items-center">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-contacts"></i>
              <div class="flex-shrink-0 avatar">
                <img src="{{:user.photo_url}}" alt="<?php __('chat::messages.person_image') ?>" class="rounded-circle" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-sidebar-right">
              </div>
              <div class="chat-contact-info flex-grow-1 ms-2">
                {{if user.project_id}}
                <h6 class="m-0"><a href="/admin/projects/{{:user.project_id}}" class="text-decoration-none text-dark">{{>user.project.name}}</a></h6>
                {{else}}
                    <h6 class="m-0">{{>user.name}}</h6>
                {{/if}}   
                             
                <small class="user-status text-muted">NextJS developer</small>
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
                  <a class="dropdown-item" href="javascript:void(0);">View Contact</a>
                  <a class="dropdown-item" href="javascript:void(0);">Mute Notifications</a>
                  <a class="dropdown-item" href="javascript:void(0);">Block Contact</a>
                  <a class="dropdown-item" href="javascript:void(0);">Clear Chat</a>
                  <a class="dropdown-item" href="javascript:void(0);">Report</a>
                </div>
              </div>
            </div>
          </div>
        </div>    
        <div class="chat__area-header position-relative">
            <div class="d-flex justify-content-between align-items-center flex-1 mx-3 chat__header-top">
                <input type="hidden" id="toId" value="{{:user.id}}">
                <input type="hidden" id="chatType" value="{{:user.id}}">
                <div class="d-flex align-items-center w-100">
                    <span class="back-to-chat d-none d-flex justify-content-center align-items-center">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </span>
                    <div class="chat__area-header-avatar">
                        <img src="{{:user.photo_url}}" alt="<?php __('chat::messages.person_image') ?>" class="img-fluid chat-header-img">
                    </div>
                    <div class="ps-3 chat__profile-avatar">
                        <h5 class="my-0 chat__area-title contact-title text-truncate">
                          {{if user.project_id}}
                            <a href="/admin/projects/{{:user.project_id}}" class="text-decoration-none text-dark">{{>user.project.name}}</a>
                          {{else}}
                           {{>user.name}}
                          {{/if}}
                        <span class="contact-title-status">
                        {{if user.is_my_contact && ~checkUserStatus(user)}}
                            <i class="nav-icon user-status-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{:user.user_status.status}}" data-original-title="{{:user.user_status.status}}">
                                {{:user.user_status.emoji}}
                            </i>
                        {{/if}}
                        </span>
                        </h5>
                        <div class="typing position-relative online-status {{if user.is_blocked || isGroup}} d-none {{/if}}" >
                            {{if user.is_online}} online {{else}} last seen at: <i>{{:lastSeenTime}}</i>{{/if}}
<!--                            <span class="chat__area-header-status"></span>-->
                            <span class="ps-3"><?php __('chat::messages.online') ?></span>
                        </div>
                    </div>
                    <div class="cursor-pointer d-xl-none ms-auto hamburger-top"
                        id="dropdownMenuButton" aria-expanded="false">
                         {{if my_role === 2}}
                        <a href="javascript:void(0)" class="text-decoration-none me-2 btn-add-members" data-group-id="{{:user.id}}" title="<?php __('chat::messages.add_members') ?>"><img src="/images/icons/mobile-add.png" alt="add"></a>
                        {{/if}}
                        <i class="fa-solid fa-bars open-profile-menu" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="chat__area-action">
                    <!-- setting view -->
                    {{if my_role === 2 && !user.project_id}}
                    <a href="javascript:void(0)" class="text-decoration-none me-2 btn-add-members" data-group-id="{{:user.id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php echo trans('chat::messages.add_members') ?>
    " title="<?php echo trans('chat::messages.add_members') ?>"><img src="/images/icons/add.png" class="add-icon" alt="add"></a>
                    {{/if}}
                    <div class="chat__area-icon open-profile-menu ms-2">
                        <i class="fa-solid fa-gear" aria-hidden="true"></i>
                    </div>
                </div>
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
        <div class="chat-conversation" id="conversation-container"></div>
    </div>



</script>
