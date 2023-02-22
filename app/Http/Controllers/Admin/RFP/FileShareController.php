<?php

namespace App\Http\Controllers\Admin\RFP;

use App\DataTables\Admin\RFP\SharedFilesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\FileShare;
use App\Models\RFPDraft;
use App\Models\RFPFile;
use App\Notifications\Admin\FileShared;
use App\Notifications\Admin\FileUpdated;
use Illuminate\Http\Request;

class FileShareController extends Controller
{
  public function index($draft_rfp, $file, SharedFilesDataTable $dataTable)
  {
    $draft_rfp = RFPDraft::mine()->findOrFail($draft_rfp);
    $dataTable->setDraftRFP($draft_rfp);
    return $dataTable->render('admin.pages.rfp.file-share.index', compact('draft_rfp'));
    // return view('admin.pages.rfp.file-share.index');
  }

  public function create($draft_rfp, $file)
  {
    $data['file'] = $file = RFPFile::mine()->findOrFail($file);
    $data['share'] = new FileShare();
    $data['users'] = Admin::whereDoesntHave('programs', function ($q) use ($file) {
      $q->where('program_id', $file->rfp->program_id);
    })->whereDoesntHave('sharedFiles', function ($q) use ($file) {
      $q->where('rfp_file_id', $file->id);
    })->get();
    $data['permissions'] = FileShare::Permissions;

    return $this->sendRes('success', ['view_data' =>  view('admin.pages.rfp.file-share.edit', $data)->render()]);
  }

  public function store(Request $request, $draft_rfp, $file)
  {
    $file = RFPFile::mine()->findOrFail($file);
    $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id|unique:file_shares,user_id,NULL,id,rfp_file_id,' . $file->id,
      'permission' => 'required|in:' . implode(',', array_keys(FileShare::Permissions)),
      'expires_at' => 'nullable|date|after:yesterday|date_format:Y-m-d',
    ]);
    try {
      foreach ($request->users as $user) {
        $file_share = $file->shares()->create([
          'user_id' => $user,
          'permission' => $request->permission,
          'expires_at' => $request->expires_at,
          'shared_by' => auth()->id(),
        ]);
        \Notification::send($file->rfp->program->programUsers()->where('id', '!=', auth()->id()), new FileShared($file_share));
        \Notification::send($file->sharedUsers->where('id', '!=', auth()->id()), new FileShared($file_share, ['data' => ['url' => route('admin.shared_files.index')]]));
        $file->createLog('Shared File with ' . Admin::find($user)->full_name . ' with ' . FileShare::Permissions[$request->permission] . ' permission' . ($request->expires_at ? ' till ' . $request->expires_at : ''));
      }

      return $this->sendRes('File Shared Successfully', ['event' => 'redirect', 'url' => route('admin.draft-rfps.files.shares.index', [$draft_rfp, $file])]);
    } catch (\Exception $e) {
      return $this->sendError('Something went wrong', ['error' => $e->getMessage()], 500);
    }
  }

  public function destroy($draft_rfp, $file, $share)
  {
    try {
      $file = RFPFile::mine()->findOrFail($file);
      $share = $file->shares()->findOrFail($share);
      $file->createLog('Unshared File with ' . $share->user->full_name);
      $share->delete();
      return $this->sendRes('File Share Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'sharedfiles-table']);
    } catch (\Throwable $th) {
      return $this->sendError('Something went wrong', ['error' => $th->getMessage()], 500);
    }
  }
}
