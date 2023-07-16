<style>
  #checklist-input {
    resize: none;
    overflow: hidden;
    min-height: 40px;
    height: auto;
  }
  </style>
<div class="row d-flex">
  <form action="{{route('admin.projects.tasks.checklist-items.update', ['task' => $task->id, 'project' => $task->project_id, 'checklist_item' => $checklistItem])}}">
    @csrf
    @method('PUT')
    <div class="my-2">
      <div class="flex-grow-1  me-1">
        <textarea class="form-control" name="title" placeholder="What you wanna do today?" id="checklist-input" rows="1">{{$checklistItem->title}}</textarea>
      </div>
      <div class="row mt-2">
        <div class="col-6"></div>
        <div class="d-flex justify-conent-end col-6">
          {!! Form::select('assigned_to', $task->assignees->pluck('email', 'id')->prepend('Assigned To', ''), $checklistItem->assigned_to, ['class' => 'form-select globalOfSelect2', 'id' => 'assigned_to']) !!}
          {!! Form::date('due_date', $checklistItem->due_date, ['class' => 'form-control mx-1', 'id' => 'due_date']) !!}
          <button class="btn btn-primary  align-self-center" data-form="ajax-form">Update</button>
        </div>
      </div>
    </div>
  </form>
</div>
