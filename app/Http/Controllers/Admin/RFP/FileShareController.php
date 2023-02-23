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
use Throwable;

class FileShareController extends Controller
{
  public function index($draft_rfp, $file, SharedFilesDataTable $dataTable)
  {
    $data['draft_rfp'] = $draft_rfp = RFPDraft::mine()->findOrFail($draft_rfp);
    $dataTable->setDraftRFP($draft_rfp);
    $data['files'] = RFPFile::where('rfp_id', $draft_rfp->id)->distinct()->pluck('title', 'id');
    $fileKeys = array_keys($data['files']->toArray());
    $data['sharedBy'] = Admin::whereHas('sharedByFiles', function ($q) use ($fileKeys) {
      $q->whereIn('rfp_file_id', $fileKeys);
    })->get();
    $data['sharedWith'] = Admin::whereHas('sharedFiles', function ($q) use ($fileKeys) {
      $q->whereIn('rfp_file_id', $fileKeys);
    })->get();
    $data['permissions'] = FileShare::Permissions;
    $data['statuses'] = FileShare::Statuses;
    return $dataTable->render('admin.pages.rfp.file-share.index', $data);
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
        \Notification::send($file->sharedUsers->where('id', '!=', auth()->id()), new FileShared($file_share, ['data' => ['url' => route('admin.shared-files.index')]]));
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

  public function revoke(Request $request, $draft_rfp, $file, $share)
  {
    try {
      $file = RFPFile::mine()->findOrFail($file);
      $share = $file->shares()->findOrFail($share);
      if ($request->isMethod('post')) {
        $share->update(['revoked_by' => auth()->id()]);
        $file->createLog('Revoked File Share with ' . $share->user->full_name);

        $notiData['title'] = auth()->user()->full_name . ' Revoked file sharing';
        $notiData['image'] = auth()->user()->avatar;
        $notiData['user'] = auth()->user();
        \Notification::send($file->sharedUsers->where('id', '!=', auth()->id()), new FileUpdated($file, $notiData + ['url' => route('admin.shared-files.index')]));
        \Notification::send($file->rfp->program->programUsers()->where('id', '!=', auth()->id()), new FileUpdated($file, $notiData));
        return $this->sendRes('File Share Revoked Successfully', ['event' => 'table_reload', 'table_id' => 'sharedfiles-table', 'close' => 'modal']);
      } elseif ($request->isMethod('get')) {
        return $this->sendRes('Confirmation', ['view_data' => view('admin.pages.rfp.file-share.revoke-confirmation', compact('share'))->render()]);
      }
    } catch (\Throwable $th) {
      $this->sendError('Something went wrong', ['error' => $th->getMessage()]);
    }
  }

  public function reinvite(Request $request, $draft_rfp, $file, $share)
  {
    $file = RFPFile::mine()->findOrFail($file);
    $share = $file->shares()->whereNull('revoked_by')->where('expires_at', '<', today())->findOrFail($share);
    if ($request->isMethod('post')) {
      $request->validate([
        'permission' => 'required|in:' . implode(',', array_keys(FileShare::Permissions)),
        'expires_at' => 'nullable|date|after:yesterday|date_format:Y-m-d',
      ]);
      $file_share = $file->shares()->create([
        'user_id' => $share->user_id,
        'permission' => $request->permission,
        'expires_at' => $request->expires_at,
        'shared_by' => auth()->id(),
      ]);
      \Notification::send($file->rfp->program->programUsers()->where('id', '!=', auth()->id()), new FileShared($file_share));
      \Notification::send($file->sharedUsers->where('id', '!=', auth()->id()), new FileShared($file_share, ['data' => ['url' => route('admin.shared-files.index')]]));
      $file->createLog('Reshared File with ' . $share->user->full_name . ' with ' . FileShare::Permissions[$request->permission] . ' permission' . ($request->expires_at ? ' till ' . $request->expires_at : ''));

      return $this->sendRes('File ReShared Successfully', ['event' => 'table_reload', 'table_id' => 'sharedfiles-table', 'close' => 'modal']);
    } elseif ($request->isMethod('get')) {
      $permissions = FileShare::Permissions;
      return $this->sendRes('Reinvite Form', ['view_data' => view('admin.pages.rfp.file-share.re-invite', compact('share', 'permissions'))->render()]);
    }
  }
}
