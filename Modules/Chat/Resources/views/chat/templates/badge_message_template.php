<script id="tmplMessageBadges" type="text/x-jsrender">
        <div  id="message-badges" class="mt-4 message-{{:id}} {{if !status}} unread {{/if}}" data-message_id="{{:id}}">
           <h5 class="d-flex justify-content-center"><span class="alert alert-primary fs-6" data-bs-toggle="tooltip" data-bs-placement="top" title="{{:~getLocalDate(created_at)}}">{{:message}}</span></h5>
        </div>

</script>
