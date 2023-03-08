<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\DataTables\WorkflowsDataTable;
use App\Http\Controllers\Controller;

class WorkflowController extends Controller
{
  public function index(WorkflowsDataTable $dataTable)
  {
    $data['title'] = 'Workflows';
    return $dataTable->render('admin.pages.workflow.approval-workflow.index', $data);
    // return view('admin.pages.workflow.approval-workflow.index');
  }
}
