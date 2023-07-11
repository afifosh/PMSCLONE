<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Plank\Mediable\Exceptions\MediaUploadException;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;

class TaskFileController extends Controller
{
  public function index($project, Task $task)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.files-index', compact('task'))->render()]);
  }

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

  public function destroy($project, Task $task, Media $file)
  {
    $file->delete();
    // $task->detachMedia($file);
    return $this->sendRes('File deleted successfully', []);
  }
}
