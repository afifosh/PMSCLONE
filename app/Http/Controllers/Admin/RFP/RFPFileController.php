<?php

namespace App\Http\Controllers\Admin\RFP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RFPDraft;
use App\Models\RFPFile;
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

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
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
    $draft_rfp->files()->create([
      'uploaded_by' => auth()->id(),
      'file' => $path,
      'title' => $file->getClientOriginalName(),
      'mime_type' => $mimes->getMimeType($file->getClientOriginalExtension()),
      'extension' => $file->getClientOriginalExtension(),
    ]);
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
    unlink($file->getPathname());
    return $this->sendRes('Uploaded Successfully', ['event' => 'page_reload', 'close' => 'modal']);
  }

  public function editFileWithOffice($file, Request $request)
  {
    $file = RFPFile::where('id', $file)->mine()->firstOrFail();
    // if (!$file->shares()->where('user_id', auth()->id())->first()) {
    //   return back()->with('error', 'something went wrong');
    // }
    $data['api_url'] = 'http://146.190.123.183/web-apps/apps/api/documents/api.js';
    // $data['api_url'] = 'http://localhost:8060/web-apps/apps/api/documents/api.js';
    $data['file_url'] = Storage::url($file->file);
    // dd($data);
    $data['file'] = $file;

    // $payload = '{
    //     "document": {
    //         "key": "Khirz6zTPdfd7",
    //         "permissions": {
    //             "comment": true,
    //             "commentGroups": {
    //                 "edit": ["Group2", ""],
    //                 "remove": [""],
    //                 "view": ""
    //             },
    //             "copy": true,
    //             "deleteCommentAuthorOnly": false,
    //             "download": true,
    //             "edit": true,
    //             "editCommentAuthorOnly": false,
    //             "fillForms": true,
    //             "modifyContentControl": true,
    //             "modifyFilter": true,
    //             "print": true,
    //             "review": true,
    //             "reviewGroups": ["Group1", "Group2", ""]
    //         },
    //         "url": "https://example.com/url-to-example-document.docx"
    //     },
    //     "editorConfig": {
    //         "callbackUrl": "https://example.com/url-to-callback.ashx",
    //         "mode": "edit",
    //         "user": {
    //             "group": "Group1",
    //             "id": "78e1e841",
    //             "name": "Smith"
    //         }
    //     }
    // }';


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
    // "documentType": "word",
    // env('APP_URL').'/only-office.php?file='.$file->file
    $payload = json_decode($data['payload'], true);
    // dd($payload);
    $data['token'] = JWT::encode($payload, config('onlyoffice.secret'), 'HS256');


    return view('admin.pages.rfp.files.only-office-editor', $data);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\File  $file
   * @return \Illuminate\Http\Response
   */
  public function show(File $file)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\File  $file
   * @return \Illuminate\Http\Response
   */
  public function edit(File $file)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateFileRequest  $request
   * @param  \App\Models\File  $file
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateFileRequest $request, File $file)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\File  $file
   * @return \Illuminate\Http\Response
   */
  public function destroy(File $file)
  {
    //
  }
}
