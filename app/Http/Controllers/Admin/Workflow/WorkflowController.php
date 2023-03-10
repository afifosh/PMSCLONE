<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\DataTables\WorkflowsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApprovalWorkflowUpdateRequest;
use App\Models\Admin;
use App\Models\ApprovalLevel;
use App\Models\Workflow;

class WorkflowController extends Controller
{
  public function index(WorkflowsDataTable $dataTable)
  {
    $data['title'] = 'Workflows';
    return $dataTable->render('admin.pages.workflow.approval-workflow.index', $data);
    // return view('admin.pages.workflow.approval-workflow.index');
  }

  public function edit(Workflow $workflow)
  {
    $data['levels'] = ApprovalLevel::where('workflow_id', $workflow->id)->with('approvers')->get();
    $data['admins'] = Admin::get();
    $data['workflow'] = $workflow;
    return view('admin.pages.workflow.approval-workflow.edit', $data);
  }

  public function update(Workflow $workflow, ApprovalWorkflowUpdateRequest $request)
  {
    $workflow->update(['name' => $request->workflow_name]);
    $active_ids = [];
    foreach ($request->level as $level) {
      $uLevel = $workflow->levels()->updateOrCreate(['id' => $level['id']], ['name' => $level['name']]);
      $uLevel->approvers()->sync(array_unique($level['approvers']));
      $active_ids[] = $uLevel->id;
    }
    $workflow->levels()->whereNotIn('id', $active_ids)->delete();

    return $this->sendRes('Workflow Updated Successfully', ['event' => 'redirect', 'url' => route('admin.workflows.index')]);
  }
}
