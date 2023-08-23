<script id="tmplConversationsList" type="text/x-jsrender">
<li class="chat-contact-list-item contact-area" data-chat-pinned={{>contact.is_pinned != 0}}>
    <a class="chat__person-box d-flex align-items-center" class="chat__person-box" data-id="{{:contactId}}" data-is_group="{{:contact.is_group}}" id="user-{{:contactId}}" data-is_my_contact="{{:~checkForMyContact(contactId)}}">
        <div class="flex-shrink-0 avatar 
        {{if contact.is_group}} 
            avatar-group
        {{else}} 
            {{if showStatus && is_online}} 
                avatar-online 
            {{else}} 
                avatar-offline
            {{/if}}
        {{/if}}">
            <img src="{{:contactDetail.photo_url}}" alt="<?php __('chat::messages.person_image') ?>" class="rounded-circle">
        </div>
        <div class="chat-contact-info flex-grow-1 ms-2">
            <h6 class="chat-contact-name text-truncate m-0 chat__person-box-name contact-name">
                {{if contactDetail.project_id}}
                    {{>contactDetail.project.name}}
                {{else}}
                    {{>contactDetail.name}}
                {{/if}}
            </h6>
            <p class="chat-contact-status text-muted text-truncate mb-0">
            {{if !~getDraftMessage(contactId)}}
                    {{:~getMessageByItsTypeForChatList(contact.message, contact.message_type, contact.file_name)}}
                {{else}}
                    {{:~getDraftMessage(contactId)}}
                {{/if}}              
            </p>
        </div>
        </a>
        <div class="d-flex flex-column align-items-end justify-content-between h-100">
           <small class="text-muted mb-auto">{{:~getLocalDate(contact.created_at)}}</small>
         {{if contact.unread_count > 0}} 
           <span class="badge badge-center rounded-pill bg-primary d-block mt-1">{{:contact.unread_count}}</span> 
        {{/if}}
        <i class="ti ti-dots-vertical d-none dropdown dropdown-trigger mt-1" id="chat-msg-action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
               <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chat-msg-action">
                  <a class="dropdown-item text-center chat__person-box-delete more-delete-option">
                        <?php echo __('chat::messages.chats.delete') ?>
                        </a>
                        <a class="dropdown-item text-center chat__person-box-pin" data-is-pinned={{>contact.is_pinned != 0}}>
                          {{if contact.is_pinned}}
                            <?php echo __('chat::messages.chats.unpin') ?>
                          {{else}}
                            <?php echo __('chat::messages.chats.pin') ?>
                          {{/if}}
                        </a>
                        <a class="dropdown-item text-center chat__person-box-archive">
                              <?php echo __('chat::messages.chats.archive_chat') ?>
                          </a>
                        {{if !(contact.is_group && contact.group.project_id)}}
                          <a class="dropdown-item text-center chat__person-box-archive">
                              <?php echo __('chat::messages.chats.archive_chat') ?>
                          </a>
                        {{/if}}                  
                </div>
        </div>
</li>
</script>
