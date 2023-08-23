<script id="tmplConversationsList" type="text/x-jsrender">
<li class="chat-contact-list-item contact-area" data-chat-pinned={{>contact.is_pinned != 0}}>>
    <a class="d-flex align-items-center">
        <div class="flex-shrink-0 avatar avatar-online">
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
        <small class="text-muted mb-auto">{{:~getLocalDate(contact.created_at)}}</small>
    </a>
</li>
</script>
