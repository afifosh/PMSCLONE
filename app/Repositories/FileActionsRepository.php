<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class FileActionsRepository
{
  public function downloadFile($file_path, $download_title, $disk = '', $options = [])
  {
    $disk = $disk ? $disk : config('filesystems.default');

    if (Storage::disk($disk)->exists($file_path)) {
      $download_title = isset($options['extension']) ? str_replace("." . $options['extension'], "", $download_title) . '.' . $options['extension'] : $download_title;
      return Storage::download($file_path, $download_title);
    }
  }

  public function previewFile($file_path, $disk = '')
  {
    $file = Storage::disk('public')->get($file_path);
    $contentType = Storage::disk($disk)->mimeType($file_path);
    return response($file, 200)->header('Content-Type', $contentType);
  }

  public function moveFile($file_path, $new_path, $disk = '')
  {
    $disk = $disk ? $disk : config('filesystems.default');

    if (Storage::disk($disk)->exists($file_path)) {
      return Storage::disk($disk)->move($file_path, $new_path);
    }
    return false;
  }
}
