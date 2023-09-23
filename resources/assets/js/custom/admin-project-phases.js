/**
 * App Email
 */

// 'use strict';

// document.addEventListener('DOMContentLoaded', function () {
  $(document).ready(function () {
    initDragola();
    const emailList = document.querySelector('.email-list'),
      emailListItems = [].slice.call(document.querySelectorAll('.task-item')),
      emailFilters = document.querySelector('.email-filters'),
      emailFilterByFolders = [].slice.call(document.querySelectorAll('.email-filter-folders li')),
      appEmailSidebar = document.querySelector('.app-email-sidebar'),
      appOverlay = document.querySelector('.app-overlay'),
      emailSearch = document.querySelector('.email-search-input');

    // Initialize PerfectScrollbar
    // ------------------------------
    // Email list scrollbar
    if (emailList) {
      let emailListInstance = new PerfectScrollbar(emailList, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Sidebar tags scrollbar
    if (emailFilters) {
      new PerfectScrollbar(emailFilters, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Search email based on searched text
    if (emailSearch) {
      emailSearch.addEventListener('keyup', e => {
        let searchValue = e.currentTarget.value.toLowerCase(),
          searchEmailListItems = {},
          selectedFolderFilter = document.querySelector('.email-filter-folders .active').getAttribute('data-target');

        // Filter emails based on selected folders
        if (selectedFolderFilter != 'inbox') {
          searchEmailListItems = [].slice.call(
            document.querySelectorAll('.task-item[data-' + selectedFolderFilter + '="true"]')
          );
        } else {
          searchEmailListItems = [].slice.call(document.querySelectorAll('.task-item'));
        }

        // console.log(searchValue);
        searchEmailListItems.forEach(searchEmailListItem => {
          let searchEmailListItemText = searchEmailListItem.textContent.toLowerCase();
          if (searchValue) {
            if (-1 < searchEmailListItemText.indexOf(searchValue)) {
              searchEmailListItem.classList.add('d-block');
            } else {
              searchEmailListItem.classList.add('d-none');
            }
          } else {
            searchEmailListItem.classList.remove('d-none');
          }
        });
      });
    }

    // Filter based on folder type (Inbox, Sent, Draft etc...)
    emailFilterByFolders.forEach(emailFilterByFolder => {
      emailFilterByFolder.addEventListener('click', e => {
        let currentTarget = e.currentTarget,
          currentTargetData = currentTarget.getAttribute('data-target');

        appEmailSidebar.classList.remove('show');
        appOverlay.classList.remove('show');

        // Remove active class from each folder filters
        Helpers._removeClass('active', emailFilterByFolders);
        // Add active class to selected folder filters
        currentTarget.classList.add('active');
        emailListItems.forEach(emailListItem => {
          // If folder filter is Inbox
          if (currentTargetData == 'inbox') {
            emailListItem.classList.add('d-block');
            emailListItem.classList.remove('d-none');
          } else if (emailListItem.hasAttribute('data-' + currentTargetData)) {
            emailListItem.classList.add('d-block');
            emailListItem.classList.remove('d-none');
          } else {
            emailListItem.classList.add('d-none');
            emailListItem.classList.remove('d-block');
          }
        });
      });
    });
    // Refresh Mails

    // if (refreshEmails && emailList) {
    //   let emailListJq = $('.email-list'),
    //     emailListInstance = new PerfectScrollbar(emailList, {
    //       wheelPropagation: false,
    //       suppressScrollX: true
    //     });
    //   // ? Using jquery vars due to BlockUI jQuery dependency
    //   refreshEmails.addEventListener('click', e => {
    //     emailListJq.block({
    //       message: '<div class="spinner-border text-primary" role="status"></div>',
    //       timeout: 1000,
    //       css: {
    //         backgroundColor: 'transparent',
    //         border: '0'
    //       },
    //       overlayCSS: {
    //         backgroundColor: '#000',
    //         opacity: 0.1
    //       },
    //       onBlock: function () {
    //         emailListInstance.settings.suppressScrollY = true;
    //       },
    //       onUnblock: function () {
    //         emailListInstance.settings.suppressScrollY = false;
    //       }
    //     });
    //   });
    // }

    // Email contacts (select2)
    // ? Using jquery vars due to select2 jQuery dependency
    let emailContacts = $('#emailContacts');
    function initSelect2() {
      if (emailContacts.length) {
        function renderContactsAvatar(option) {
          if (!option.id) {
            return option.text;
          }
          let $avatar =
            "<div class='d-flex flex-wrap align-items-center'>" +
            "<div class='avatar avatar-xs me-2'>" +
            "<img src='" +
            assetsPath +
            'img/avatars/' +
            $(option.element).data('avatar') +
            "' alt='avatar' class='rounded-circle' />" +
            '</div>' +
            option.text +
            '</div>';

          return $avatar;
        }
        emailContacts.wrap('<div class="position-relative"></div>').select2({
          placeholder: 'Select value',
          dropdownParent: emailContacts.parent(),
          closeOnSelect: false,
          templateResult: renderContactsAvatar,
          templateSelection: renderContactsAvatar,
          escapeMarkup: function (es) {
            return es;
          }
        });
      }
    }
    initSelect2();
    Echo.private(`projects.${active_project}`).listen('.project-phase-updated', e => {
      const update_on = ['phase-list'];
      if(update_on.includes(e.modifiedTab)){
        refreshPhaseList();
      }
    });
  });
  function refreshPhaseList(project_id){
    project_id = project_id || window.active_project;
    $.ajax({
      type: "get",
      url: route('admin.projects.contracts.stages.phases.index', {project: project_id, contract: active_contract, stage: active_stage}),
      success: function (response) {
        $('.tasks-list').html(response.data.view_data)
        $('.myTasksCount').text(response.data.myTasksCount)
      }
    });
  }

  function updateEmptyTaskStatus() {
    const mainTasks = document.querySelectorAll('.todo-task-list > li');

    mainTasks.forEach(task => {
        const subtasks = task.querySelectorAll('.subtasks > li');
        if (subtasks.length === 0) {
            task.classList.add('empty-task');
        } else {
            task.classList.remove('empty-task');
        }
    });
  }
  function initDragola(){
    const drake = dragula({
      isContainer: function (el) {
        return el.classList.contains('subtasks') || el.classList.contains('todo-task-list');
      },
      moves: function (el, container, handle) {
        return handle.classList.contains('drag-handle'); // Use the drag-icon as the handle for both tasks and subtasks
      },
      accepts: function (el, target, source, sibling) {
         if (el.classList.contains('task') && target.classList.contains('subtasks')) {
            return false;
          }

          if (el.classList.contains('subtask') && !target.classList.contains('subtasks')) {
            return false;
          }
        return true;
      }
    });
    drake.on('drag', function () {
      document.body.classList.add('dragging');
      updateEmptyTaskStatus();
    });

    drake.on('dragend', function () {
        document.body.classList.remove('dragging');
        updateEmptyTaskStatus();
    });

    drake.on('drop', function(el, target, source, sibling){
      // tasks have sub tasks and both can be sorted so
      let sortedTasks = [];
      $('.task').each(function (index, element) {
        sortedTasks.push($(element).data('id'));
      });
      const uniquePhases = [];
      const seenIds = new Set();

      sortedTasks.forEach(id => {
          if (!seenIds.has(id)) {
              uniquePhases.push(id);
              seenIds.add(id);
          }
      });

      $.ajax({
        type: "put",
        url: route('admin.projects.contracts.sort-phases', {project : active_project, contract: active_contract}),
        data: {
          phases: uniquePhases
        },
        complete: function(data) {
          refreshPhaseList();
        }
      });
    })
  }
// });
