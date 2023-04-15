<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Innoclapps\Contracts\Repositories\MediaRepository;

class MediaViewController extends Controller
{
    /**
     * Initialize new MediaViewController instance.
     *
     * @param \App\Innoclapps\Contracts\Repositories\MediaRepository $media
     */
    public function __construct(protected MediaRepository $repository)
    {
    }

    /**
     * Preview media file
     *
     * @param string $token
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show($token)
    {
        $media = $this->repository->findByTokenOrFail($token);

        return view('media.preview', compact('media'));
    }

    /**
     * Download media file
     *
     * @param string $token
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($token)
    {
        $media = $this->repository->findByTokenOrFail($token);

        return Storage::disk($media->disk)->download($media->getDiskPath());
    }

    /**
     * Preview media file
     *
     * @param string $token
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function preview($token)
    {
        $media = $this->repository->findByTokenOrFail($token);
        $disk  = Storage::disk($media->disk);

        return $disk->response($media->getDiskPath(), null, [
            'Pragma'        => 'public',
            'Cache-Control' => 'max-age=86400, public',
            'Content-Type'  => $media->mime_type,
            'Expires'       => gmdate('D, d M Y H:i:s \G\M\T', time() + 86400 * 7), // 7 days
            'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', $disk->lastModified($media->getDiskPath())),
        ]);
    }
}
