/***
   * Actions can be taken when contract is updated
   **/
const tabActions = {
  'overview' : function(){
    location.reload();
  },
  'stages' : function(){
    $('#globalModal').modal('hide');
    $('#stages-table').DataTable().ajax.reload();
  },
  'phases' : function(){
    $('#globalModal').modal('hide');
    $('#phases-table').DataTable().ajax.reload();
  }
}

/*************************************************************************
 ********************  Listening for contract updates  *******************
 **************************************************************************/

/***
 *  Echo listen fo contract updates
 **/
Echo.join(`contracts.${activeContractId}`)
    .here((users) => {
      for (let index = 0; index < users.length; index++) {
        const user = users[index];
        contractViewingUsers[user.id] = user;
      }
      updateViewingUsers(contractViewingUsers);
    })
    .joining((user) => {
      contractViewingUsers[user.id] = user;
      updateViewingUsers(contractViewingUsers);
    })
    .leaving((user) => {
      delete contractViewingUsers[user.id];
      updateViewingUsers(contractViewingUsers);
    })
    .listen('.contract-updated', e => {
      if(e.modifiedTab == activeContractTab){
        tabActions[activeContractTab]();
      }
    });

/***
 * Update the users viewing contract
 **/
function updateViewingUsers(users){
  $('#contract-viewing-users').html(getUserAvatarGroup(users));
  $('[data-popup="tooltip-custom"]').tooltip();
}

/***
 * Get the html for user avatar group
 **/
function getUserAvatarGroup(users, size = 'md'){
  let html = '<ul class="list-unstyled d-flex align-items-center avatar-group mb-0">';
  users.forEach(user => {
    html += `<li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="${user.name}"
                class="avatar avatar-${size} pull-up">
                <img class="rounded-circle" src="${user.avatar}" alt="Avatar">
            </li>`;
  });
  html += '</ul>';
  return html;
}

/***************************************************************************
 ********************  End Listening for contract updates  ****************
 **************************************************************************/

/**************************************************************************
 ********************  Echo for stage updates ****************************
 * ***********************************************************************/
// Join the stage channel
$('#globalModal').on('shown.bs.modal', function (e) {
  if($('#globalModal #stage-update-form').length){
    // Echo listen fo stage updates
    Echo.join(`contract-stages.${$('#globalModal #stage-update-form').data('stage-id')}`)
        .here((users) => {
          for (let index = 0; index < users.length; index++) {
            const user = users[index];
            stageEditingUsers[user.id] = user;
          }
          updateStageEditingUsers(stageEditingUsers);
        })
        .joining((user) => {
          stageEditingUsers[user.id] = user;
          updateStageEditingUsers(stageEditingUsers);
        })
        .leaving((user) => {
          delete stageEditingUsers[user.id];
          updateStageEditingUsers(stageEditingUsers);
        })
        .error((error) => {
            console.error(error);
        });
  }
});

/***
 * Whisper stage editing
 * */
$('#globalModal').on('keyup', '#stage-update-form input', function (e) {
    Echo.private(`contract-stages.${$('#globalModal #stage-update-form').data('stage-id')}`)
        .whisper('editing-model', {
            stage:{
              name: $('#globalModal #stage-update-form input[name="name"]').val(),
            }
        });
});

/***
 * Listen For Whisper stage editing on modal open
 * */
$('#globalModal').on('shown.bs.modal', function (e) {
  if($('#globalModal #stage-update-form').length){
    Echo.private(`contract-stages.${$('#globalModal #stage-update-form').data('stage-id')}`)
        .listenForWhisper('editing-model', (e) => {
            $('#globalModal #stage-update-form input[name="name"]').val(e.stage.name);
        });
  }
});

// Leave the stage channel when modal is closed
$('#globalModal').on('hidden.bs.modal', function (e) {
  if($('#globalModal #stage-update-form').length){
    Echo.leave(`contract-stages.${$('#globalModal #stage-update-form').data('stage-id')}`);
  }
});
/***
 * Update the users Editing same stage
 **/
function updateStageEditingUsers(users){
  $('.stage-editing-users').html(getUserAvatarGroup(users, 'sm'));
  $('[data-popup="tooltip-custom"]').tooltip();
}

/***************************************************************************
 ********************  End Echo for stage updates  ************************
 **************************************************************************/

/**************************************************************************
 * ********************  Echo for phase updates  **************************
 * ***********************************************************************/
// Join the phase channel
$('#globalModal').on('shown.bs.modal', function (e) {
  if($('#globalModal #phase-update-form').length){
    // Echo listen fo phase updates
    Echo.join(`contract-phases.${$('#globalModal #phase-update-form').data('phase-id')}`)
        .here((users) => {
          for (let index = 0; index < users.length; index++) {
            const user = users[index];
            phaseEditingUsers[user.id] = user;
          }
          updatePhaseEditingUsers(phaseEditingUsers);
        })
        .joining((user) => {
          phaseEditingUsers[user.id] = user;
          updatePhaseEditingUsers(phaseEditingUsers);
        })
        .leaving((user) => {
          delete phaseEditingUsers[user.id];
          updatePhaseEditingUsers(phaseEditingUsers);
        })
        .error((error) => {
            console.error(error);
        });
  }
});

/***
 * Whisper phase editing
 * */
$('#globalModal').on('keyup change paste', '#phase-update-form input, #phase-update-form select, #phase-update-form textarea, #phase-update-form checkbox', function (e) {
    if(disablePhaseWhisper){
      return;
    }
    whisperForPhaseEditing();
});

function whisperForPhaseEditing()
{
  Echo.private(`contract-phases.${$('#globalModal #phase-update-form').data('phase-id')}`)
        .whisper('editing-model', {
            data: {
              name: $('#globalModal #phase-update-form [name="name"]').val(),
              estimated_cost: $('#globalModal #phase-update-form [name="estimated_cost"]').val(),
              'phase_taxes[]': $('#globalModal #phase-update-form [name="phase_taxes[]"]').val(),
              total_cost: $('#globalModal #phase-update-form [name="total_cost"]').val(),
              start_date: $('#globalModal #phase-update-form [name="start_date"]').val(),
              due_date: $('#globalModal #phase-update-form [name="due_date"]').val(),
              calc_end_date: $('#globalModal #phase-update-form [name="calc_end_date"]').val(),
              cal_end_date_unit: $('#globalModal #phase-update-form [name="cal_end_date_unit"]').val(),
              description: $('#globalModal #phase-update-form [name="description"]').val(),
            }
        });
}

/***
 * Listen For Whisper phase editing on modal open
 * */
$('#globalModal').on('shown.bs.modal', function (e) {
  if($('#globalModal #phase-update-form').length){
    Echo.private(`contract-phases.${$('#globalModal #phase-update-form').data('phase-id')}`)
        .listenForWhisper('editing-model', (e) => {
            disablePhaseWhisper = true;
            $.each(e.data, function (name, value) {
              $('#globalModal #phase-update-form [name="'+name+'"]').val(value);
            });
            $('#globalModal #phase-update-form [name="phase_taxes[]"]').trigger('change');
            disablePhaseWhisper = false;
        });
  }
});

// Leave the phase channel when modal is closed
$('#globalModal').on('hidden.bs.modal', function (e) {
  if($('#globalModal #phase-update-form').length){
    Echo.leave(`contract-phases.${$('#globalModal #phase-update-form').data('phase-id')}`);
  }
});
/***
 * Update the users Editing same phase
 **/
function updatePhaseEditingUsers(users){
  $('.phase-editing-users').html(getUserAvatarGroup(users, 'sm'));
  $('[data-popup="tooltip-custom"]').tooltip();
}

/***************************************************************************
 ********************  End Echo for phase updates  ************************
 **************************************************************************/
