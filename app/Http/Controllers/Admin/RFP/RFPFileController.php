<?php

namespace App\Http\Controllers\Admin\RFP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RFPDraft;
use App\Models\RFPFile;
use App\Repositories\FileUploadRepository;
use Illuminate\Support\Facades\Storage;
use Firebase\JWT\JWT;

class RFPFileController extends Controller
{
  public function index()
  {
    // $data = new \stdClass();
    // $data->users = User::where('id', '!=', auth()->id())->get();
    // $data->files = FileShare::where('user_id', auth()->id())->with('file')->get();

    // return view('admin.files', compact('data'));
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

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreFileRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, FileUploadRepository $file_repo)
  {
    $request->validate([
      'file' => 'required|mimes:doc,docx,pdf,png,jpg,jpeg,txt,xls,xlsx,csv,ppt,pptx',
      'Draft_RFP' => 'required|exists:rfp_drafts,id'
    ]);
    // $path = $request->file('file')->store('/');
    $draft_rfp = RFPDraft::where('id', $request->Draft_RFP)->mine()->firstOrFail();
    $path = $file_repo->addAttachment($request->file('file'), '');
    $draft_rfp->files()->create([
      'uploaded_by' => auth()->id(),
      'file' => $path,
      'title' => $request->file('file')->getClientOriginalName()
    ]);
    // set v1
    $histDir = getHistoryDir(getStoragePath($path));  // get the history directory
    // turn the file information into the json format
    $json = [
      "created" => date("Y-m-d H:i:s"),
      "id" => auth()->id(),
      "name" => auth()->user()->name,
    ];

    // write the encoded file information to the createdInfo.json file
    file_put_contents($histDir . DIRECTORY_SEPARATOR . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));

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
              "fileType": "'.$data['ext'].'",
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
          "documentType": "word",
          "editorConfig": {
              "callbackUrl": "' . route('update-file', $file) . '",
              "mode": "edit",
              "user": {
                  "group": "Group1",
                  "id": "' . auth()->id() . '",
                  "name": "' . auth()->user()->name . '"
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
