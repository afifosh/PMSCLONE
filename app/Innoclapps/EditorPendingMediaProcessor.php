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

namespace App\Innoclapps;

use Stringable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use KubAT\PhpSimple\HtmlDomParser;
use App\Innoclapps\Contracts\Repositories\MediaRepository;
use App\Innoclapps\Contracts\Repositories\PendingMediaRepository;

class EditorPendingMediaProcessor
{
    /**
     * The media directory where the pending media
     * will be moved after save/create
     *
     * @var string
     */
    protected static string $mediaDir = 'editor';

    protected static string $verifyMediaRegex = '/[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}/m';

    /**
     * @var \App\Innoclapps\Contracts\Repositories\MediaRepository
     */
    protected MediaRepository $media;

    /**
     * @var \App\Innoclapps\Contracts\Repositories\PendingMediaRepository
     */
    protected PendingMediaRepository $pendingMedia;

    /**
     * Initialize new EditorPendingMediaProcessor instance.
     */
    public function __construct()
    {
        $this->media        = resolve(MediaRepository::class);
        $this->pendingMedia = resolve(PendingMediaRepository::class);
    }

    /**
     * Process editor fields by given new and original content
     *
     * @param string|\Stringable $newContent
     * @param string|\Stringable $originalContent
     *
     * @return void
     */
    public function process($newContent, $originalContent) : void
    {
        if ($newContent instanceof Stringable) {
            $newContent = (string) $newContent;
        }

        if ($originalContent instanceof Stringable) {
            $originalContent = (string) $originalContent;
        }

        // First store the current media tokens
        $tokens = $this->getMediaTokensFromContent($newContent);

        // After that only process the new media
        $this->processNewMedia($tokens);

        // Finaly removed any removed media
        $this->media->deleteByTokens(
            $this->getRemovedMedia($originalContent, $tokens)
        );
    }

    /**
     * Process editor pending media via given model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array|string $field
     *
     * @return void
     */
    public function processViaModel($model, array|string $fields) : void
    {
        foreach (Arr::wrap($fields) as $field) {
            $value = $model->{$field};

            if ($value instanceof Stringable) {
                $value = (string) $value;
            }

            // First store the current media tokens
            $tokens = $this->getMediaTokensFromContent($value);

            // After that only process the new media
            $this->processNewMedia($tokens);

            // Check if it's update, if yes, get the removed media tokens and delete them
            if (! $model->wasRecentlyCreated) {
                $originalValue = $model->getOriginal($field);

                if ($value instanceof Stringable) {
                    $originalValue = (string) $originalValue;
                }

                $this->media->deleteByTokens(
                    $this->getRemovedMedia($originalValue, $tokens)
                );
            }
        }
    }

    /**
     * Delete all media via model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array|string $fields
     *
     * @return void
     */
    public function deleteAllViaModel($model, array|string $fields) : void
    {
        foreach (Arr::wrap($fields) as $field) {
            $this->deleteAllByContent($model->{$field});
        }
    }

    /**
     * Delete media by given content
     *
     * @return void
     */
    public function deleteAllByContent($content) : void
    {
        $this->media->deleteByTokens(
            $this->getMediaTokensFromContent($content)
        );
    }

    /**
     * Process the new media by given tokens
     *
     * @param array $tokens
     *
     * @return void
     */
    protected function processNewMedia($tokens) : void
    {
        // Handle all pending medias and move them to the appropriate
        // directory and also delete the pending record from the pending table after move
        // From the current, we will get only the pending which are not yet processed
        $this->pendingMedia->getByToken($tokens)->each(function ($pending) {
            $pending->attachment->move(static::$mediaDir, Str::random(40));
            $this->pendingMedia->delete($pending->id);
        });
    }

    /**
     * Get the removed media from the editor content
     *
     * @param string|array|null $originalContent
     * @param string|array $newContent
     *
     * @return array
     */
    protected function getRemovedMedia($originalContent, $newContent) : array
    {
        if ($newContent instanceof Stringable) {
            $newContent = (string) $newContent;
        }

        if ($originalContent instanceof Stringable) {
            $originalContent = (string) $originalContent;
        }

        if (is_null($originalContent)) {
            return [];
        }

        return array_diff(
            is_string($originalContent) ? $this->getMediaTokensFromContent($originalContent) : $originalContent,
            is_string($newContent) ? $this->getMediaTokensFromContent($newContent) : $newContent
        );
    }

    /**
     * Get the media current token via the content
     *
     * @param string $content
     *
     * @return array
     */
    protected function getMediaTokensFromContent($content) : array
    {
        if ($content instanceof Stringable) {
            $content = (string) $content;
        }

        return array_merge(
            $this->getMediaTokensFromImagesAndVideos($content),
            $this->getMediaTokensInlineBackgroundImages($content),
        );
    }

    protected function getMediaTokensFromImagesAndVideos(?string $content) : array
    {
        $sources = [];

        if (! $dom = HtmlDomParser::str_get_html($content)) {
            return $sources;
        }

        // Process images and videos
        foreach ($dom->find('img,source') as $element) {
            if ($src = $element->getAttribute('src')) {
                // Check if is really media by checking the uuid in the image or video src
                preg_match(static::$verifyMediaRegex, $src, $matches);

                if (count($matches) === 1) {
                    $sources[] = $matches[0];
                }
            }
        }

        return $sources;
    }

    protected function getMediaTokensInlineBackgroundImages(?string $content) : array
    {
        $sources = [];

        if (! $content) {
            return $sources;
        }

        $bgImageMediaRegex = '/background\-image:(?: {1,}|)url(?: {1,}|)\([\'|"](.*)[\'|"]\)/';

        preg_match_all($bgImageMediaRegex, html_entity_decode($content, ENT_QUOTES), $bgImages, PREG_SET_ORDER, 0);

        foreach ($bgImages as $match) {
            if (preg_match(static::$verifyMediaRegex, $match[1], $matches)) {
                $sources[] = $matches[0];
            }
        }

        return $sources;
    }
}
