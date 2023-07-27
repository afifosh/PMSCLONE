<?php

namespace App\Http\Controllers\Admin\Project;

use App\Events\Admin\ProjectTaskUpdatedEvent;
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
    abort_if(!$task->project->isMine(), 403);

    $task->load('media');

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.files-index', compact('task'))->render()]);
  }

  public function store($project, Task $task, Request $request)
  {
    abort_if(!$task->project->isMine(), 403);
    try {
      $media = MediaUploader::fromSource($request->file('file'))
        ->toDirectory('task-files')
        ->upload();
      $task->attachMedia($media, 'attachment');

      $message = auth()->user()->name . ' uploaded a file in task : "' . $task->subject . '"';
      broadcast(new ProjectTaskUpdatedEvent($task, 'files', $message))->toOthers();

      return $this->sendRes('File uploaded successfully', []);
    } catch (MediaUploadException $e) {
      /** @var \Symfony\Component\HttpKernel\Exception\HttpException */
      $exception = $this->transformMediaUploadException($e);

      return $this->response(['message' => $exception->getMessage()], $exception->getStatusCode());
    }
  }

  public function destroy($project, Task $task, Media $file)
  {
    abort_if(!$task->project->isMine(), 403);
    $message = auth()->user()->name . ' removed a file from task : "' . $task->subject . '"';

    $file->delete();
    // $task->detachMedia($file);

    broadcast(new ProjectTaskUpdatedEvent($task, 'files', $message))->toOthers();

    return $this->sendRes('File deleted successfully', []);
  }
}
