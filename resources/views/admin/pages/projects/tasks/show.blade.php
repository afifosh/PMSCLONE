<style>
  .nav-tabs-shadow {
    box-shadow : none !important;
  }
</style>
<div class="row">
    <div class="nav-align-top nav-tabs-shadow">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link {{request()->tab != 'comments' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-summary" aria-controls="navs-top-summary" aria-selected="true">Summary</button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-checklist" aria-controls="navs-top-checklist" aria-selected="false">Checklist</button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-files" aria-controls="navs-top-files" aria-selected="false">Files</button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-activities" aria-controls="navs-top-activities" aria-selected="false">Activities</button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{request()->tab == 'comments' ? 'active' : ''}}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comments" aria-controls="navs-top-comments" aria-selected="false">Comments</button>
        </li>
      </ul>
      <div class="tab-content p-0">
        <div class="tab-pane fade {{request()->tab != 'comments' ? 'show active' : ''}}" id="navs-top-summary" role="tabpanel">
          @include('admin.pages.projects.tasks.show-summary')
        </div>
        <div class="tab-pane fade" id="navs-top-checklist" role="tabpanel">
          @include('admin.pages.projects.tasks.show-checklist')
        </div>
        <div class="tab-pane fade" id="navs-top-files" role="tabpanel">
          @include('admin.pages.projects.tasks.show-files')
        </div>
        <div class="tab-pane fade" id="navs-top-activities" role="tabpanel">
          @include('admin.pages.projects.tasks.show-activities')
        </div>
        <div class="tab-pane fade {{request()->tab == 'comments' ? 'show active' : ''}}" id="navs-top-comments" role="tabpanel">
          @include('admin.pages.projects.tasks.show-comments')
        </div>
      </div>
    </div>
</div>
<script>
  function create_reminder(id = null){
    $('[name="reminder_id"]').val(null);
    $('#create-reminder-form').toggle();
  }

   function update_reminder_form(){
    var form = $('#create-reminder-form');
    form.hide();
    var reminder_id = form.find('[name="reminder_id"]').val();
    var remind_at = form.find('[name="remind_at"]').val();
    var recipient_id = form.find('[name="recipient_id"]').val();
    var description = form.find('[name="description"]').val();

    if(reminder_id == null){
      form.before('<div class="alert alert-info">Reminder will be created</div>')
    }
  }
</script>
