<script id="tmplGroupDetails" type="text/x-jsrender">

       <div class="sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-4 pt-5">
          <div class="avatar avatar-xl">
          <div class="bg-white rounded-circle text-dark position-absolute text-center p-1 mt-5 ms-5">
          {{if group_type === 2}}
          <i class="fa-solid fa-lock closed-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can send messages into the group."> </i>
          {{/if}}
          {{if privacy === 2}}
          <i class="fa-solid fa-shield private-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can add or remove members from the group."> </i>
        {{/if}}
</div>

            <img src="{{:photo_url}}" alt="" class="img-fluid user-about-image img-circle rounded-circle" id="groupDetailsImage-{{:id}}">
          </div>
          <h6 id="groupDetailsName-{{:id}}" class="mt-2 mb-0">
                {{if project_id && project}}
                  <a href="/admin/projects/{{:project_id}}" class="text-decoration-none text-dark">{{:project.name}}</a>
                {{else}}
                  {{:name}}
                {{/if}}
          </h6>
          <span class="mt-2">Created By {{:group_created_by}}, {{:~getLocalDate(created_at, 'DD/MM/YYYY')}} </span>
          <i class="ti ti-x ti-sm cursor-pointer close-sidebar d-block" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-sidebar-right"></i>
        </div>

      <div class="sidebar-body px-4 pb-4 ps ps--active-y">

      <div class="group-profile-data group-about-sidebar">
        <div class="chat-profile__divider"></div>
        {{if project_id && project}}
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Category') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsCategory-{{:id}}">
                {{:project.category.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Program') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsProgram-{{:id}}">
                {{:project.program.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Start Date') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsStartDate-{{:id}}">
                {{:project.start_date}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Deadline') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDeadline-{{:id}}">
                {{:project.deadline}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Tags') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsTags-{{:id}}">
                {{:project.tags.join(', ')}}
              </p>
          </div>
        {{/if}}
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title"><?php echo trans('chat::messages.group.description') ?></h6>
            <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDescription-{{:id}}">
                {{if (project_id && project) || description}}
                    {{if project_id && project}}
                      {{:project.description}}
                    {{else}}
                      {{:description}}
                    {{/if}}
                {{else}}
                    No description added yet...
                {{/if}}
            </p>
        </div>
        <div class="chat-profile__divider"></div>
            <nav class="nav nav-pills m-3" id="myTab" role="tablist">
                <a class="nav-item nav-link active group-members-tab" id="nav-group-members-tab" data-bs-toggle="tab" href="#nav-group-members"
                   role="tab" aria-controls="nav-group-members " aria-expanded="true"><?php echo trans('chat::messages.participants') ?><span class="badge badge-pill badge-secondary ms-2 members-count" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="Total group members">{{:members_count}}</span></a>
                <a class="nav-item nav-link" id="nav-group-medias-tab" data-bs-toggle="tab" href="#nav-group-medias"
                   role="tab" aria-controls="nav-group-medias"><?php echo trans('chat::messages.media') ?></a>
            </nav>
            <div class="tab-content white-border" id="nav-tabContent">
                <div class="tab-pane fade show active div-group-members-nav" id="nav-group-members" role="tabpanel"
                                 aria-labelledby="nav-group-members-tab">
                    <p class="chat-profile__column-title-detail text-muted mb-0 group-participants"></p>
                    {{for users}}
                    <div class="chat__person-box group-member-{{:id}} {{if ~root.logged_in_user_id === id }} non-clickable {{/if}}" data-id="{{:id}}" data-is_group="0" id="user-{{:id}}">
                        <div class="position-relative chat__person-box-status-wrapper">
                            <div class="chat__person-box-avtar chat__person-box-avtar--active">
                                <img src="{{:photo_url}}" alt="person image" class="user-avatar-img">
                            </div>
                        </div>
                        <div class="chat__person-box-detail">
                            <h5 class="mb-1 chat__person-box-name contact-name">{{:name}}
                            <span class="group-user-status">
                                {{if ~checkUserStatusForGroupMember(user_status)}}
                                    <i class="nav-icon user-status-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{:user_status.status}}" data-original-title="{{:user_status.status}}">
                                        {{:user_status.emoji}}
                                    </i>
                                {{/if}}
                            </span>
                            </h5>
                             {{if pivot.role === 2}}
                                <span class="badge badge-pill badge-primary">{{if ~root.created_by === id}} Owner {{else}} Admin{{/if}}</span>
                            {{/if}}
                        </div>
                         {{if ~root.created_by !== id && ~root.my_role === 2 && ~root.logged_in_user_id != id && !~root.removed_from_group}}
                            <div class="chat__person-box-msg-time">
                                <div class="chat__person-box-group" data-member-id="{{:id}}" data-group-id="{{:~root.id}}">
                                  {{if !~root.project_id}}
                                   <div class="btn-group">
                                      <i class="fa-solid fa-ellipsis-vertical group-details-bar" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      </i>
                                      <div class="dropdown-menu member-options-{{:id}}">
                                        <a class="dropdown-item remove-member-from-group" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="removeMember-{{:id}}">Remove Member</a>
                                       {{if pivot.role === 2}}
                                        <a class="dropdown-item dismiss-admin-member" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="dismissAdmin-{{:id}}">Dismiss As Admin</a>
                                        {{else}}
                                             <a class="dropdown-item make-member-to-group-admin" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="makeAdmin-{{:id}}">Make Admin</a>
                                        {{/if}}
                                      </div>
                                    </div>
                                  {{/if}}
                                </div>
                            </div>
                        {{/if}}
                    </div>
                    {{/for}}
                    {{if users.length === 0}}
                        <p class="no-group-members-found text-center">No group members found...</p>
                    {{/if}}
                </div>
                <div class="tab-pane fade show" id="nav-group-medias" role="tabpanel"
                         aria-labelledby="nav-group-medias-tab">
                    <div class="chat-profile__column--media">

                        <div class="chat-profile__media-container">
                            {{if media && media.length}}
                                {{for media}}
                                    {{:~prepareMedia(#data)}}
                                {{/for}}
                            {{else}}
                                <span class="no-photo-found text-muted">No media shared yet...</span>
                            {{/if}}
                        </div>
                    </div>
                </div>
            </div>

    {{if project_id }}
      <div class="chat-profile__divider"></div>
      {{if privacy === 2 && my_role === 2 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
      </div>
      {{else privacy === 1 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
    </div>
      {{/if}}
      {{if !group_deleted_by_owner && removed_from_group || (created_by === logged_in_user_id)}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-delete-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.delete_group') ?></a>
      </div>
      {{else !removed_from_group}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-leave-from-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.leave_group') ?></a>
      </div>
      {{/if}}
    {{/if}}
    
        <div class="my-4">
          <small class="text-muted text-uppercase"><?php echo trans('chat::messages.about') ?></small>
          <p class="mb-0 mt-3">A Next. js developer is a software developer who uses the Next. js framework alongside ReactJS to build web applications.</p>
        </div>
        <div class="my-4">
          <small class="text-muted text-uppercase">Personal Information</small>
          <ul class="list-unstyled d-grid gap-2 mt-3">
            <li class="d-flex align-items-center">
              <i class="ti ti-mail ti-sm"></i>
              <span class="align-middle ms-2">josephGreen@email.com</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="ti ti-phone-call ti-sm"></i>
              <span class="align-middle ms-2">+1(123) 456 - 7890</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="ti ti-clock ti-sm"></i>
              <span class="align-middle ms-2">Mon - Fri 10AM - 8PM</span>
            </li>
          </ul>
        </div>
        <div class="mt-4">
          <small class="text-muted text-uppercase">Options</small>
          <ul class="list-unstyled d-grid gap-2 mt-3">
            <li class="cursor-pointer d-flex align-items-center">
              <i class="ti ti-badge ti-sm"></i>
              <span class="align-middle ms-2">Add Tag</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class="ti ti-star ti-sm"></i>
              <span class="align-middle ms-2">Important Contact</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class="ti ti-photo ti-sm"></i>
              <span class="align-middle ms-2">Shared Media</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class="ti ti-trash ti-sm"></i>
              <span class="align-middle ms-2">Delete Contact</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class="ti ti-ban ti-sm"></i>
              <span class="align-middle ms-2">Block Contact</span>
            </li>
          </ul>
        </div>
    <div class="chat-profile__header">
        <span class="chat-profile__about"><?php echo trans('chat::messages.about') ?></span>
        <div>
        {{if !removed_from_group && my_role === 2 && !project_id}}
            <a href="javascript:void(0)" class="text-decoration-none edit-group text-center me-2" data-id="{{:id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php echo trans('chat::messages.edit_group') ?>"><img src="/images/icons/mobile-edit.png" alt="edit"></a>
        {{/if}}
            <i class="fa-solid fa-xmark chat-profile__close-btn"></i>
        </div>
    </div>
    <div class="chat-profile__person--active mb-2 profile__inner m-auto">
        <div class="chat-profile__avatar text-center chat-profile__img-wrapper group-profile-image">
         {{if group_type === 2}}
          <i class="fa-solid fa-lock closed-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can send messages into the group."> </i>
          {{/if}}
          {{if privacy === 2}}
          <i class="fa-solid fa-shield private-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can add or remove members from the group."> </i>
        {{/if}}
            <img src="{{:photo_url}}" alt="" class="img-fluid user-about-image img-circle" id="groupDetailsImage-{{:id}}">
        </div>
    </div>
    <div class="chat-profile__person-last-seen chat-profile__column mb-0">
     <div class="divGroupDetails d-flex justify-content-around row">
            <div class="col-12">
            <h4 id="groupDetailsName-{{:id}}" class="align-items-center mb-0">
              {{if project_id && project}}
                <a href="/admin/projects/{{:project_id}}" class="text-decoration-none text-dark">{{:project.name}}</a>
              {{else}}
                {{:name}}
              {{/if}}
              </h4>
            </div>
            <span class="mt-2">Created By {{:group_created_by}}, {{:~getLocalDate(created_at, 'DD/MM/YYYY')}} </span>
    </div>
    </div>

    <div class="group-profile-data group-about-sidebar">
        <div class="chat-profile__divider"></div>
        {{if project_id && project}}
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Category') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsCategory-{{:id}}">
                {{:project.category.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Program') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsProgram-{{:id}}">
                {{:project.program.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Start Date') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsStartDate-{{:id}}">
                {{:project.start_date}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Deadline') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDeadline-{{:id}}">
                {{:project.deadline}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Tags') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsTags-{{:id}}">
                {{:project.tags.join(', ')}}
              </p>
          </div>
        {{/if}}
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title"><?php echo trans('chat::messages.group.description') ?></h6>
            <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDescription-{{:id}}">
                {{if (project_id && project) || description}}
                    {{if project_id && project}}
                      {{:project.description}}
                    {{else}}
                      {{:description}}
                    {{/if}}
                {{else}}
                    No description added yet...
                {{/if}}
            </p>
        </div>
        <div class="chat-profile__divider"></div>
            <nav class="nav nav-pills m-3" id="myTab" role="tablist">
                <a class="nav-item nav-link active group-members-tab" id="nav-group-members-tab" data-bs-toggle="tab" href="#nav-group-members"
                   role="tab" aria-controls="nav-group-members " aria-expanded="true"><?php echo trans('chat::messages.participants') ?><span class="badge badge-pill badge-secondary ms-2 members-count" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="Total group members">{{:members_count}}</span></a>
                <a class="nav-item nav-link" id="nav-group-medias-tab" data-bs-toggle="tab" href="#nav-group-medias"
                   role="tab" aria-controls="nav-group-medias"><?php echo trans('chat::messages.media') ?></a>
            </nav>
            <div class="tab-content white-border" id="nav-tabContent">
                <div class="tab-pane fade show active div-group-members-nav" id="nav-group-members" role="tabpanel"
                                 aria-labelledby="nav-group-members-tab">
                    <p class="chat-profile__column-title-detail text-muted mb-0 group-participants"></p>
                    {{for users}}
                    <div class="chat__person-box group-member-{{:id}} {{if ~root.logged_in_user_id === id }} non-clickable {{/if}}" data-id="{{:id}}" data-is_group="0" id="user-{{:id}}">
                        <div class="position-relative chat__person-box-status-wrapper">
                            <div class="chat__person-box-avtar chat__person-box-avtar--active">
                                <img src="{{:photo_url}}" alt="person image" class="user-avatar-img">
                            </div>
                        </div>
                        <div class="chat__person-box-detail">
                            <h5 class="mb-1 chat__person-box-name contact-name">{{:name}}
                            <span class="group-user-status">
                                {{if ~checkUserStatusForGroupMember(user_status)}}
                                    <i class="nav-icon user-status-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{:user_status.status}}" data-original-title="{{:user_status.status}}">
                                        {{:user_status.emoji}}
                                    </i>
                                {{/if}}
                            </span>
                            </h5>
                             {{if pivot.role === 2}}
                                <span class="badge badge-pill badge-primary">{{if ~root.created_by === id}} Owner {{else}} Admin{{/if}}</span>
                            {{/if}}
                        </div>
                         {{if ~root.created_by !== id && ~root.my_role === 2 && ~root.logged_in_user_id != id && !~root.removed_from_group}}
                            <div class="chat__person-box-msg-time">
                                <div class="chat__person-box-group" data-member-id="{{:id}}" data-group-id="{{:~root.id}}">
                                  {{if !~root.project_id}}
                                   <div class="btn-group">
                                      <i class="fa-solid fa-ellipsis-vertical group-details-bar" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      </i>
                                      <div class="dropdown-menu member-options-{{:id}}">
                                        <a class="dropdown-item remove-member-from-group" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="removeMember-{{:id}}">Remove Member</a>
                                       {{if pivot.role === 2}}
                                        <a class="dropdown-item dismiss-admin-member" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="dismissAdmin-{{:id}}">Dismiss As Admin</a>
                                        {{else}}
                                             <a class="dropdown-item make-member-to-group-admin" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="makeAdmin-{{:id}}">Make Admin</a>
                                        {{/if}}
                                      </div>
                                    </div>
                                  {{/if}}
                                </div>
                            </div>
                        {{/if}}
                    </div>
                    {{/for}}
                    {{if users.length === 0}}
                        <p class="no-group-members-found text-center">No group members found...</p>
                    {{/if}}
                </div>
                <div class="tab-pane fade show" id="nav-group-medias" role="tabpanel"
                         aria-labelledby="nav-group-medias-tab">
                    <div class="chat-profile__column--media">

                        <div class="chat-profile__media-container">
                            {{if media && media.length}}
                                {{for media}}
                                    {{:~prepareMedia(#data)}}
                                {{/for}}
                            {{else}}
                                <span class="no-photo-found text-muted">No media shared yet...</span>
                            {{/if}}
                        </div>
                    </div>
                </div>
            </div>

    {{if project_id }}
      <div class="chat-profile__divider"></div>
      {{if privacy === 2 && my_role === 2 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
      </div>
      {{else privacy === 1 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
    </div>
      {{/if}}
      {{if !group_deleted_by_owner && removed_from_group || (created_by === logged_in_user_id)}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-delete-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.delete_group') ?></a>
      </div>
      {{else !removed_from_group}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-leave-from-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.leave_group') ?></a>
      </div>
      {{/if}}
    {{/if}}
</script>


<script id="tmplGroupDetailsOLD" type="text/x-jsrender">
    <div class="chat-profile__header">
        <span class="chat-profile__about"><?php echo trans('chat::messages.about') ?></span>
        <div>
        {{if !removed_from_group && my_role === 2 && !project_id}}
            <a href="javascript:void(0)" class="text-decoration-none edit-group text-center me-2" data-id="{{:id}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php echo trans('chat::messages.edit_group') ?>"><img src="/images/icons/mobile-edit.png" alt="edit"></a>
        {{/if}}
            <i class="fa-solid fa-xmark chat-profile__close-btn"></i>
        </div>
    </div>
    <div class="chat-profile__person--active mb-2 profile__inner m-auto">
        <div class="chat-profile__avatar text-center chat-profile__img-wrapper group-profile-image">
         {{if group_type === 2}}
          <i class="fa-solid fa-lock closed-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can send messages into the group."> </i>
          {{/if}}
          {{if privacy === 2}}
          <i class="fa-solid fa-shield private-group-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="The admin only can add or remove members from the group."> </i>
        {{/if}}
            <img src="{{:photo_url}}" alt="" class="img-fluid user-about-image img-circle" id="groupDetailsImage-{{:id}}">
        </div>
    </div>
    <div class="chat-profile__person-last-seen chat-profile__column mb-0">
     <div class="divGroupDetails d-flex justify-content-around row">
            <div class="col-12">
            <h4 id="groupDetailsName-{{:id}}" class="align-items-center mb-0">
              {{if project_id && project}}
                <a href="/admin/projects/{{:project_id}}" class="text-decoration-none text-dark">{{:project.name}}</a>
              {{else}}
                {{:name}}
              {{/if}}
              </h4>
            </div>
            <span class="mt-2">Created By {{:group_created_by}}, {{:~getLocalDate(created_at, 'DD/MM/YYYY')}} </span>
    </div>
    </div>

    <div class="group-profile-data group-about-sidebar">
        <div class="chat-profile__divider"></div>
        {{if project_id && project}}
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Category') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsCategory-{{:id}}">
                {{:project.category.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Program') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsProgram-{{:id}}">
                {{:project.program.name}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Start Date') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsStartDate-{{:id}}">
                {{:project.start_date}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Deadline') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDeadline-{{:id}}">
                {{:project.deadline}}
              </p>
          </div>
          <div class="chat-profile__column">
              <h6 class="chat-profile__column-title"><?php echo trans('Tags') ?></h6>
              <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsTags-{{:id}}">
                {{:project.tags.join(', ')}}
              </p>
          </div>
        {{/if}}
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title"><?php echo trans('chat::messages.group.description') ?></h6>
            <p class="chat-profile__column-title-detail text-muted mb-0 group-desc" id="groupDetailsDescription-{{:id}}">
                {{if (project_id && project) || description}}
                    {{if project_id && project}}
                      {{:project.description}}
                    {{else}}
                      {{:description}}
                    {{/if}}
                {{else}}
                    No description added yet...
                {{/if}}
            </p>
        </div>
        <div class="chat-profile__divider"></div>
            <nav class="nav nav-pills m-3" id="myTab" role="tablist">
                <a class="nav-item nav-link active group-members-tab" id="nav-group-members-tab" data-bs-toggle="tab" href="#nav-group-members"
                   role="tab" aria-controls="nav-group-members " aria-expanded="true"><?php echo trans('chat::messages.participants') ?><span class="badge badge-pill badge-secondary ms-2 members-count" data-bs-toggle="tooltip" data-bs-placement="top"
                               title="Total group members">{{:members_count}}</span></a>
                <a class="nav-item nav-link" id="nav-group-medias-tab" data-bs-toggle="tab" href="#nav-group-medias"
                   role="tab" aria-controls="nav-group-medias"><?php echo trans('chat::messages.media') ?></a>
            </nav>
            <div class="tab-content white-border" id="nav-tabContent">
                <div class="tab-pane fade show active div-group-members-nav" id="nav-group-members" role="tabpanel"
                                 aria-labelledby="nav-group-members-tab">
                    <p class="chat-profile__column-title-detail text-muted mb-0 group-participants"></p>
                    {{for users}}
                    <div class="chat__person-box group-member-{{:id}} {{if ~root.logged_in_user_id === id }} non-clickable {{/if}}" data-id="{{:id}}" data-is_group="0" id="user-{{:id}}">
                        <div class="position-relative chat__person-box-status-wrapper">
                            <div class="chat__person-box-avtar chat__person-box-avtar--active">
                                <img src="{{:photo_url}}" alt="person image" class="user-avatar-img">
                            </div>
                        </div>
                        <div class="chat__person-box-detail">
                            <h5 class="mb-1 chat__person-box-name contact-name">{{:name}}
                            <span class="group-user-status">
                                {{if ~checkUserStatusForGroupMember(user_status)}}
                                    <i class="nav-icon user-status-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{:user_status.status}}" data-original-title="{{:user_status.status}}">
                                        {{:user_status.emoji}}
                                    </i>
                                {{/if}}
                            </span>
                            </h5>
                             {{if pivot.role === 2}}
                                <span class="badge badge-pill badge-primary">{{if ~root.created_by === id}} Owner {{else}} Admin{{/if}}</span>
                            {{/if}}
                        </div>
                         {{if ~root.created_by !== id && ~root.my_role === 2 && ~root.logged_in_user_id != id && !~root.removed_from_group}}
                            <div class="chat__person-box-msg-time">
                                <div class="chat__person-box-group" data-member-id="{{:id}}" data-group-id="{{:~root.id}}">
                                  {{if !~root.project_id}}
                                   <div class="btn-group">
                                      <i class="fa-solid fa-ellipsis-vertical group-details-bar" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      </i>
                                      <div class="dropdown-menu member-options-{{:id}}">
                                        <a class="dropdown-item remove-member-from-group" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="removeMember-{{:id}}">Remove Member</a>
                                       {{if pivot.role === 2}}
                                        <a class="dropdown-item dismiss-admin-member" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="dismissAdmin-{{:id}}">Dismiss As Admin</a>
                                        {{else}}
                                             <a class="dropdown-item make-member-to-group-admin" href="#" data-member-id="{{:id}}" data-group-id="{{:~root.id}}" id="makeAdmin-{{:id}}">Make Admin</a>
                                        {{/if}}
                                      </div>
                                    </div>
                                  {{/if}}
                                </div>
                            </div>
                        {{/if}}
                    </div>
                    {{/for}}
                    {{if users.length === 0}}
                        <p class="no-group-members-found text-center">No group members found...</p>
                    {{/if}}
                </div>
                <div class="tab-pane fade show" id="nav-group-medias" role="tabpanel"
                         aria-labelledby="nav-group-medias-tab">
                    <div class="chat-profile__column--media">

                        <div class="chat-profile__media-container">
                            {{if media && media.length}}
                                {{for media}}
                                    {{:~prepareMedia(#data)}}
                                {{/for}}
                            {{else}}
                                <span class="no-photo-found text-muted">No media shared yet...</span>
                            {{/if}}
                        </div>
                    </div>
                </div>
            </div>

    {{if project_id }}
      <div class="chat-profile__divider"></div>
      {{if privacy === 2 && my_role === 2 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
      </div>
      {{else privacy === 1 && !removed_from_group}}
      <div class="chat-profile__column pb-0">
          <a href="#" class='btn btn-primary btn-add-members' data-group-id="{{:id}}"><?php echo trans('chat::messages.chats.add_members') ?></a>
    </div>
      {{/if}}
      {{if !group_deleted_by_owner && removed_from_group || (created_by === logged_in_user_id)}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-delete-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.delete_group') ?></a>
      </div>
      {{else !removed_from_group}}
      <div class="chat-profile__column pt-1">
        <a href="#" class='btn btn-danger btn-leave-from-group' data-group-id="{{:id}}"><?php echo trans('chat::messages.group.leave_group') ?></a>
      </div>
      {{/if}}
    {{/if}}
</script>
