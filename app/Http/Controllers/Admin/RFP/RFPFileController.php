<?php

namespace App\Http\Controllers\Admin\RFP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\RFPDraft;
use App\Models\RFPFile;
use App\Notifications\Admin\FileUpdated;
use App\Notifications\Admin\FileUploaded;
use App\Repositories\FileActionsRepository;
use App\Repositories\FileUploadRepository;
use Illuminate\Support\Facades\Storage;
use Firebase\JWT\JWT;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Throwable;
use Notification;

class RFPFileController extends Controller
{
  public function index(Request $request, $draft_rfp)
  {
    $draft_rfp = RFPDraft::where('id', $draft_rfp)->mine()->with('program')->firstOrFail();

    $query = RFPFile::query();
    $query->when($request->filter, function ($q) use ($request) {
      return $q->filter($request->filter);
    });
    $files = $query->where('rfp_id', $draft_rfp->id)->latest()->get();
    return view('admin.pages.rfp.file-manager', compact('draft_rfp', 'files'));
  }

  public function files_activity_tab(Request $request, $draft_rfp)
  {
    $request->validate([
      'filter_files' => 'nullable|array|exists:rfp_file_logs,id',
      'filter_actioner' => 'nullable|array|exists:rfp_file_logs,actioner_id',
    ]);
    $draft_rfp = RFPDraft::mine()->findOrFail($draft_rfp);
    $files = $draft_rfp->files()->withTrashed()->withBin()->pluck('title', 'id');
    $users = Admin::whereHas('fileLogs', function($q) use ($files) {
      $q->whereIn('file_id', array_keys($files->toArray()));
    })->get();
    $logs = $draft_rfp->fileLogs()->applyRequestFilters()->latest()->paginate();
    return view('admin.pages.rfp.file-activity', compact('draft_rfp', 'logs', 'files', 'users'));
  }

  public function store($draft_rfp, Request $request, FileUploadRepository $file_repo)
  {

    $request->validate([
      'file' => 'required|mimetypes:text/plain,application/*,image/*,video/*,audio/*'
    ]);

    $draft_rfp = RFPDraft::where('id', $draft_rfp)->mine()->firstOrFail();

    $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
    // check if the upload is success, throw exception or return response you need
    if ($receiver->isUploaded() === false) {
      throw new UploadMissingFileException();
    }
    // receive the file
    $save = $receiver->receive();

    // check if the upload has finished (in chunk mode it will send smaller files)
    if ($save->isFinished()) {
      // save the file and return any response you need, current example uses `move` function. If you are
      // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
      return $this->saveFile($request, $save->getFile(), $draft_rfp, $file_repo);
    }

    // we are in chunk mode, lets send the current progress
    /** @var AbstractHandler $handler */
    $handler = $save->handler();

    return response()->json([
      "done" => $handler->getPercentageDone(),
      'status' => true
    ]);
  }
  public function saveFile(Request $request, $file, RFPDraft $draft_rfp, FileUploadRepository $file_repo)
  {
    $mimes = new \Mimey\MimeTypes;
    $path = $draft_rfp->id . DIRECTORY_SEPARATOR . $file_repo->addAttachment($file, $draft_rfp->id);
    $uploaded_file = $draft_rfp->files()->create([
      'uploaded_by' => auth()->id(),
      'file' => $path,
      'title' => $file->getClientOriginalName(),
      'mime_type' => $mimes->getMimeType($file->getClientOriginalExtension()),
      'extension' => $file->getClientOriginalExtension(),
    ]);

    if ($uploaded_file->is_editable()) {
      // set v1
      $histDir = getHistoryDir(getStoragePath($path));  // get the history directory
      // turn the file information into the json format
      $json = [
        "created" => date("Y-m-d H:i:s"),
        "id" => auth()->id(),
        "name" => auth()->user()->full_name,
      ];
      // write the encoded file information to the createdInfo.json file
      file_put_contents($histDir . DIRECTORY_SEPARATOR . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));
    }
    $uploaded_file->createLog('Uploaded File');
    unlink($file->getPathname());
    Notification::send($uploaded_file->rfp->program->programUsers()->where('id', '!=', auth()->id()), new FileUploaded($uploaded_file));
    return $this->sendRes('Uploaded Successfully', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function editFileWithOffice($file, $rfp = '')
  {
    $data['file'] = $file = RFPFile::mineOrShared()->findOrFail($file);
    $data['api_url'] = config('onlyoffice.doc_server_api_url');
    $payload['document'] = [
      'fileType' => $file->extension,
      'title' => $file->title,
      'url' => Storage::url($file->file),
      'key' => getDocEditorKey($file->file),
      'permissions' => [
        'comment' => $file->getMode() == 'edit' ? true : false, // commmets true for edit and comment permission, mode view only for view
        'commentGroups' => [
          'edit' => '',
          'remove' => '',
          'view' => ''
        ],
        'edit' => $file->getPermission() != 'view' ? true : false, //if the file is shared with edit permission, then the file will be opened in edit mode. false for comments and view.
        'copy' => true,
        'deleteCommentAuthorOnly' => false,
        'download' => true,
        'editCommentAuthorOnly' => false,
        'fillForms' => true,
        'modifyContentControl' => true,
        'modifyFilter' => true,
        'print' => true,
        'review' => true,
        'changeHistory' => true
      ]
    ];
    $payload['editorConfig'] = [
      'callbackUrl' => route('update-file', $file),
      'user' => [
        'id' => auth()->id(),
        'name' => auth()->user()->full_name
      ],
      'customization' => [
        'review' => [
          'trackChanges' => true
        ]
      ],
      'mode' => $file->getMode(), // 'edit' or 'view. 'edit' for edit and comments mode and 'view' for view mode
    ];
    $payload['token'] = JWT::encode($payload, config('onlyoffice.secret'), 'HS256');
    $file->createLog('Opened File In editor');
    $payload['height'] = 900;
    $data['payload'] = json_encode($payload, JSON_PRETTY_PRINT);

    $notiData['title'] = auth()->user()->full_name . ' opened file for editing';
    $notiData['image'] = auth()->user()->avatar;
    $notiData['user'] = auth()->user();
    \Notification::send($file->sharedUsers->where('id', '!=', auth()->id()), new FileUpdated($file, $notiData + ['url' => route('admin.shared-files.index')]));
    \Notification::send($file->rfp->program->programUsers()->where('id', '!=', auth()->id()), new FileUpdated($file, $notiData));
    $data['draft_rfp'] = $rfp;

    return view('admin.pages.rfp.files.only-office-editor', $data);
  }

  public function show($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::mineOrShared()->findOrFail($file);
    $file->createLog('Viewed File');

    return $file_repo->previewFile($file->file);
  }

  public function edit($draft_rfp, $file, Request $request)
  {
    $file = RFPFile::mineOrShared()->findOrFail($file);

    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.files.edit', compact('file'))->render()]);
  }

  public function update($draft_rfp, $file, Request $request)
  {
    $request->validate(['title' => 'required|string|max:255']);
    $file = RFPFile::mineOrShared()->findOrFail($file);
    $file->createLog('Renamed File from ' . pathinfo($file->title)['filename'] . ' to ' . $request->title);
    $file->update(['title' => $request->title . '.' . $file->extension]);

    return $this->sendRes('Updated Successfully', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function download($draft_rfp, $file, Request $request, FileActionsRepository $file_repo)
  {
    try {
      $file = RFPFile::mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);
      $version = $request->version ?? @getFileVersion(getHistoryDir(getStoragePath(ltrim($file->curFilePath(), '/'))));
      $file->createLog('Downloaded version' . $version);
      return $file_repo->downloadFile($file->curVerPath($version), pathinfo($file->title, PATHINFO_FILENAME) . ' version-' . $version . '.' . $file->extension, '', $file->extension);
    } catch (Throwable $e) {
      return back()->with('error', $e->getMessage());
    }
  }

  public function moveToTrash($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::mineOrShared()->findOrFail($file);
    Storage::move($file->file . '-hist', RFPFile::TRASH_PATH . $file->file . '-hist');
    $file_repo->moveFile($file->file, RFPFile::TRASH_PATH . $file->file);
    $file->update(['trashed_at' => now()]);
    $file->createLog('Moved file to trash');

    return $this->sendRes('Moved To Trash', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function destroy($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);
    if (!$file->deleted_at) {
      Storage::move(RFPFile::TRASH_PATH . $file->file . '-hist', RFPFile::DEL_PATH . $file->file . '-hist');
      $file_repo->moveFile(RFPFile::TRASH_PATH . $file->file, RFPFile::DEL_PATH . $file->file);
      $file->delete();
      $file->createLog('Deleted File');
    } else {
      $file->createLog('Deleted File Permanently');
      $file->deleteForcefully(RFPFile::DEL_PATH . $file->file);
    }

    return $this->sendRes('File Deleted', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function restoreFile($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::whereNotNull('trashed_at')->mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);
    if ($file->deleted_at) {
      $file_repo->moveFile(RFPFile::DEL_PATH . $file->file, $file->file);
      Storage::move(RFPFile::DEL_PATH . $file->file . '-hist', $file->file . '-hist');
      $file->createLog('Restored Deleted File');
    } else {
      $file_repo->moveFile(RFPFile::TRASH_PATH . $file->file, $file->file);
      Storage::move(RFPFile::TRASH_PATH . $file->file . '-hist', $file->file . '-hist');
      $file->createLog('Restored File From Trash');
    }
    $file->update(['trashed_at' => null, 'deleted_at' => null]);

    return back()->with('success', __('File Restored'));
  }

  public function getActivity($draft_rfp, $file)
  {
    $file = RFPFile::mineOrShared()->withBin()->withTrashCheck()->findOrFail($file);
    $logs = $file->logs()->latest()->limit(15)->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.files.activity-timeline', compact('logs'))->render()]);
  }

  public function toggleImportant($draft_rfp, $file)
  {
    $file = RFPFile::mine()->findOrFail($file);
    if($file->is_important)
      $file->createLog('Marked File as Important');
    else
      $file->createLog('Unmarked Important');
    $file->update(['is_important' => !$file->is_important]);
    if($file->is_important)
      return back()->with('success', __('File Marked as Important'));
    else
      return back()->with('success', __('File Unmarked Important'));
  }
}
