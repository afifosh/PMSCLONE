<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;

class AttachmentController extends Controller
{
  public function store(Invoice $invoice, Request $request, FileUploadRepository $file_repo)
  {
    $request->validate([
      'file' => 'required|mimetypes:text/plain,application/*,image/*,video/*,audio/*'
    ]);

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
      return $this->saveFile($save->getFile(), $file_repo, $invoice);
    }

    // we are in chunk mode, lets send the current progress
    /** @var AbstractHandler $handler */
    $handler = $save->handler();

    return response()->json([
      "done" => $handler->getPercentageDone(),
      'status' => true
    ]);
  }

  public function saveFile($file, FileUploadRepository $file_repo, Invoice $invoice)
  {
    // $path = Invoice::FILES_PATH . DIRECTORY_SEPARATOR  . $invoice->id;
    // $file_path = $file_repo->addAttachment($file, $path);

    // $media = MediaUploader::importPath(config('filesystems.default'), $path.'/'.$file_path)->useFilename();
    $media = MediaUploader::fromSource($file)->toDirectory(Invoice::FILES_PATH . DIRECTORY_SEPARATOR  . $invoice->id)->upload();
    $invoice->attachMedia($media, 'attachment');
    $data = [
      'id' => $media['id'],
      'name' => substr($media['filename'], 0, 10),
      'url' => $media->getDownloadUrl(),
      'del_url' => route('admin.invoices.attachments.destroy', [$invoice->id, $media['id']]),
    ];

    return $this->sendRes('Uploaded Successfully', $data);
  }

  public function destroy(Invoice $invoice, Media $attachment)
  {
    $invoice->detachMedia($attachment);

    $id = $attachment->id;

    $attachment->delete();

    return $this->sendRes('Deleted Successfully',['event' => 'functionCall', 'function' => 'removeMedia', 'function_params' => $id]);
  }
}
