<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\SharedFilesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\FileShare;
use App\Models\RFPDraft;
use App\Models\RFPFile;
use Illuminate\Http\Request;

class SharedFileController extends Controller
{
  public function index(Request $request, SharedFilesDataTable $dataTable)
  {
    // $files = RFPFile::availableShared()->filter($request->filter)->get();

    $data['files'] = RFPFile::availableShared()->distinct()->pluck('title', 'id');
    $fileKeys = array_keys($data['files']->toArray());
    $data['drafts'] = RFPDraft::whereHas('files', function($q) use ($fileKeys){
      $q->whereIn('id', $fileKeys);
    })->distinct()->pluck('name', 'id');
    $data['sharedBy'] = Admin::whereHas('sharedByFiles', function ($q) use ($fileKeys) {
      $q->whereIn('rfp_file_id', $fileKeys);
    })->get();
    $data['permissions'] = FileShare::Permissions;
    $data['statuses'] = FileShare::Statuses;

    return $dataTable->render('admin.pages.rfp.shared-files.index', $data);
    // return view('admin.pages.rfp.shared-files.index', compact('files'));
  }

  public function fileActivity($file)
  {
    $data['file']= $file = RFPFile::mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);
    $data['users'] = Admin::whereHas('fileLogs', function($q) use ($file) {
      $q->where('file_id', $file->id);
    })->get();

    $data['logs'] = $file->logs()->applyRequestFilters()->with('actioner')->latest()->paginate();

    return view('admin.pages.rfp.file-activity', $data);
  }

  public function fileVersions($file)
  {
    $data['file']= RFPFile::mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);

    return view('admin.pages.rfp.file-versions', $data);
  }
}
