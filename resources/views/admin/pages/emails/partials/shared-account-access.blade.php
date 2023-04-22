
                    <div class="modal-body">
                      <button type="button" class="btn-close" onclick="location.reload();" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center">
                        <h3 class="mb-2">Share Inbox</h3>
                        <p>Share inbox with a team member</p>
                      </div>
                    </div>
                    <div class="mb-4 pb-2">
                      <label for="select2Basic" class="form-label">Add Users</label>
                      <select
                        id="select2Basic" onchange="addUser(this);"
                        class="form-select select2 form-select-lg share-project-select"
                        data-allow-clear="true"
                      >
                      <option>Select User</option>
                    @foreach($users as $user)
                      <option value="{{$user->id}}" data-user-id="{{$user->id}}" data-avatar="{{$user->avatar}}" data-email="{{$user->email}}" data-name="{{$user->first_name. ' ' . $user->last_name}}">
                          {{$user->first_name. ' ' . $user->last_name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <!-- <h4 class="mb-4 pb-2">1 Member</h4> -->
                    <ul id="members-list" class="p-0 m-0">
          @foreach($account->users as $user)
                    <li class="d-flex flex-wrap mb-3" data-user-id="{{$user->id}}">
             <div class="avatar me-3">
                <img src="{{$user->avatar}}" alt="avatar" class="rounded-circle" />
            </div>
            <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-2">
                    <p class="mb-0">{{$user->first_name. ' ' . $user->last_name}}</p>
                    <p class="mb-0 text-muted">{{$user->email}}</p>
                </div>
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn dropdown-toggle p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span id="button-{{$user->id}}" class="text-muted fw-normal me-2 d-none d-sm-inline-block">{{$user->getPermission($account)}}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                            <a class="dropdown-item" href="javascript:permissionChanged('Revoked',{{$user->id}},0);">Revoked</a>
                        </li>
                    @foreach($module->permissions as $permission)
                    <li>
                            <a class="dropdown-item" href="javascript:permissionChanged('{{$permission->name}}',{{$user->id}},{{$permission->id}});">{{$permission->name}}</a>
                        </li>
                    @endforeach
                    
                    </ul>
                </div>
            </div>
    </li>
    @endforeach
</ul>

<script>
    // Get the select element and members list
    const select = document.getElementById('select2Basic');
    const membersList = document.getElementById('members-list');
    debugger;
    
    // Add a change event listener to the select element
     function addUser(event) {
        // Get the selected option and its data attributes
        const option = select.options[select.selectedIndex];
        const name = option.dataset.name;
        const email = option.dataset.email;
        const avatar = option.dataset.avatar;
        const userId = option.dataset.userId;
       
        const isUserAdded = [...membersList.children].some((li) => {
        const userIdAttr = li.getAttribute('data-user-id');
         return userIdAttr && userIdAttr === userId;
        });
        if(!isUserAdded){
        // Create a new li element with the selected user's information
        const li = document.createElement('li');
        li.className = 'd-flex flex-wrap mb-3';
        li.setAttribute('data-user-id', userId);
        li.innerHTML = `
            <div class="avatar me-3">
                <img src="${avatar}" alt="avatar" class="rounded-circle" />
            </div>
            <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-2">
                    <p class="mb-0">${name}</p>
                    <p class="mb-0 text-muted">${email}</p>
                </div>
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn dropdown-toggle p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span id="button-`+userId+`" class="text-muted fw-normal me-2 d-none d-sm-inline-block">Revoked</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                            <a class="dropdown-item" href="javascript:permissionChanged('Revoked',`+userId+`,0);">Revoked</a>
                        </li>
                    @foreach($module->permissions as $permission)
                    <li>
                            <a class="dropdown-item" href="javascript:permissionChanged('{{$permission->name}}',`+userId+`,{{$permission->id}});">{{$permission->name}}</a>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        `;
        
        // Append the new li element to the members list
        membersList.appendChild(li);
    }
    }//);
    function permissionChanged(permission,user_id,permission_id){
      $('#button-'+user_id).html(permission);
        var url="{{url('/admin/mail/accounts/:accountId/setPermission')}}";
        url=url.replace(':accountId',{{$account->id}});
      $.ajax({
      url:url,
      method:'post',
      data:{user_id:user_id,permission_id:permission_id},
      success:function(response){
        toastr.success(response);
      },
      error : function(jqXHR, textStatus, errorThrown) {
        toastr.error(errorThrown);
    }}
    );
    
    }
    $('.select2').select2();
</script>

            
           