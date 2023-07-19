Echo.private(`projects.${active_project}`).listen('.project-updated', e => {
  console.log('.project-updated');
  console.log(e);
  if (e.modifiedTab == 'summary') {
    handleSummaryUpdate(e);
  } else if (e.modifiedTab == 'new_task_added') {
    newTaskAdded(e);
  } else if (e.modifiedTab == 'checklist' && typeof reload_task_checklist == 'function') {
    reload_task_checklist();
  } else if (e.modifiedTab == 'files' && typeof reload_task_files == 'function') {
    reload_task_files();
  } else if (e.modifiedTab == 'activity' && typeof reload_task_activity == 'function') {
    reload_task_activity();
  } else if (e.modifiedTab == 'comments' && typeof reload_task_comments == 'function') {
    reload_task_comments();
  }
});

function handleSummaryUpdate(e) {
  refreshTasksDatatable();
}

function newTaskAdded(e) {
  refreshTasksDatatable();
}

function refreshTasksDatatable() {
  $('#project-tasks-datatable').DataTable().ajax.reload();
}
