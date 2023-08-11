<style>
  #checklist-input {
    resize: none;
    overflow: hidden;
    min-height: 40px;
    height: auto;
  }
  </style>
<div class="row d-flex">
  <form action="{{route('admin.projects.tasks.checklist-items.store', ['task' => $task->id, 'project' => $task->project_id])}}">
    @csrf
    @method('POST')
    <div class="my-2">
      <div class="flex-grow-1  me-1">
        <textarea class="form-control" name="title" placeholder="What you wanna do today?" id="checklist-input" rows="1"></textarea>
      </div>
      <div class="row mt-2">
        <div class="col-4"></div>
        <div class="d-flex justify-conent-end col-8">
          {!! Form::select('assigned_to', $task->assignees->pluck('email', 'id')->prepend('Assigned To', ''), null, ['class' => 'form-select globalOfSelect2', 'id' => 'assigned_to']) !!}
          {!! Form::date('due_date', null, ['class' => 'form-control mx-1 flatpickr', 'id' => 'due_date']) !!}
          <button class="btn btn-primary  align-self-center" data-form="ajax-form">Save</button>
        </div>
      </div>
    </div>
  </form>
</div>
