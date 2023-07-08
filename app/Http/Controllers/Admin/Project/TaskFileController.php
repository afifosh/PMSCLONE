<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Plank\Mediable\Exceptions\MediaUploadException;
use Plank\Mediable\Facades\MediaUploader;

class TaskFileController extends Controller
{
  public function store($project, Task $task, Request $request)
  {
    try {
      $media = MediaUploader::fromSource($request->file('file'))
        ->toDirectory('task-files')
        ->upload();
      $task->attachMedia($media, 'attachment');
      return $this->sendRes('File uploaded successfully', []);
    } catch (MediaUploadException $e) {
      /** @var \Symfony\Component\HttpKernel\Exception\HttpException */
      $exception = $this->transformMediaUploadException($e);

      return $this->response(['message' => $exception->getMessage()], $exception->getStatusCode());
    }
  }
}
