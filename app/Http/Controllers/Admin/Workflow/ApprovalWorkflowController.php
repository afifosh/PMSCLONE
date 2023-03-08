<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\DataTables\WorkflowsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApprovalWorkflowUpdateRequest;
use App\Models\Admin;
use App\Models\ApprovalLevel;
use App\Models\Workflow;

class ApprovalWorkflowController extends Controller
{
  public function index(WorkflowsDataTable $dataTable)
  {
    $data['title'] = 'Approval Workflows';
    $dataTable->slug = 'company-verification-workflow';
    return $dataTable->render('admin.pages.workflow.approval-workflow.index', $data);
    // return view('admin.pages.workflow.approval-workflow.index');
  }

  public function edit(Workflow $approval_workflow)
  {
    $data['levels'] = ApprovalLevel::where('workflow_id', $approval_workflow->id)->with('approvers')->orderBy('order')->get();
    $data['admins'] = Admin::get();
    $data['workflow'] = $approval_workflow;
    return view('admin.pages.workflow.approval-workflow.edit', $data);
  }

  public function update(Workflow $approval_workflow, ApprovalWorkflowUpdateRequest $request)
  {
    $approval_workflow->update(['name' => $request->workflow_name]);
    foreach ($request->name as $key => $name) {
      $level = ApprovalLevel::find($key);
      $level->name = $name;
      $level->save();
      $level->approvers()->sync($request->approvers[$key]);
    }

    return $this->sendRes('Workflow Updated Successfully', ['event' => 'redirect', 'url' => route('admin.approval-workflows.index')]);
  }
}
