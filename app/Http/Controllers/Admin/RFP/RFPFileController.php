<?php

namespace App\Http\Controllers\Admin\RFP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RFPDraft;
use App\Models\RFPFile;
use App\Repositories\FileActionsRepository;
use App\Repositories\FileUploadRepository;
use Illuminate\Support\Facades\Storage;
use Firebase\JWT\JWT;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

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
    return $this->sendRes('Uploaded Successfully', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function editFileWithOffice($file, Request $request)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    $data['api_url'] = config('onlyoffice.doc_server_api_url');
    $data['file_url'] = Storage::url($file->file);
    $data['file'] = $file;
    $data['ext'] = pathinfo(Storage::path($file->file), PATHINFO_EXTENSION);
    $data['payload'] = '{
          "document": {
              "fileType": "' . $data['ext'] . '",
              "title": "' . $file->title . '",
              "url": "' . $data['file_url'] . '",
              "key": "' . getDocEditorKey($file->file) . '",
              "permissions": {
                  "comment": true,
                  "commentGroups": {
                      "edit": "",
                      "remove": "",
                      "view": ""
                  },
                  "copy": true,
                  "deleteCommentAuthorOnly": false,
                  "download": true,
                  "edit": true,
                  "editCommentAuthorOnly": false,
                  "fillForms": true,
                  "modifyContentControl": true,
                  "modifyFilter": true,
                  "print": true,
                  "review": true,
                  "changeHistory" : true,
                  "reviewGroups": ["Group1", "Group2", ""]
              }
          },
          "editorConfig": {
              "callbackUrl": "' . route('update-file', $file) . '",
              "mode": "edit",
              "user": {
                  "group": "Group1",
                  "id": "' . auth()->id() . '",
                  "name": "' . auth()->user()->full_name . '"
              },
              "customization": {
                  "review": {
                  "trackChanges": true
                  }
              }
          }
      }';
    // env('APP_URL').'/only-office.php?file='.$file->file
    $payload = json_decode($data['payload'], true);
    $data['token'] = JWT::encode($payload, config('onlyoffice.secret'), 'HS256');
    $file->createLog('Opened File For Editing');

    return view('admin.pages.rfp.files.only-office-editor', $data);
  }

  public function show($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    $file->createLog('Opened File');

    return $file_repo->previewFile($file->file);
  }

  public function edit($draft_rfp, $file, Request $request)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();

    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.files.edit', compact('file'))->render()]);
  }

  public function update($draft_rfp, $file, Request $request)
  {
    $request->validate(['title' => 'required|string|max:255']);
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    $file->createLog('Renamed File from ' . $file->title . ' to ' . $request->title);
    $file->update(['title' => $request->title]);

    return $this->sendRes('Updated Successfully', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function download($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    $file->createLog('Downloaded File');

    return $file_repo->downloadFile($file->file, $file->title, '', ['extension' => $file->extension]);
  }

  public function destroy($draft_rfp, $file, FileActionsRepository $file_repo)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    $file_repo->moveFile($file->file, 'trash' . DIRECTORY_SEPARATOR . 'draft-files' . DIRECTORY_SEPARATOR . $file->file);
    $file->update(['trashed_at' => now()]);
    $file->createLog('Deleted File');

    return $this->sendRes('Moved To Trash', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function getActivity($draft_rfp, $file)
  {
    $file = RFPFile::where('id', $file)->mine()->with('logs.actioner')->firstOrFail();

    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.files.activity-timeline', compact('file'))->render()]);
  }
}
