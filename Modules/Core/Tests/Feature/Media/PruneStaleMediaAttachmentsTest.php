<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Tests\Feature\Media;

use Illuminate\Support\Carbon;
use Modules\Core\Media\PruneStaleMediaAttachments;
use Modules\Core\Models\Media;
use Tests\TestCase;

class PruneStaleMediaAttachmentsTest extends TestCase
{
    public function test_it_prunes_stale_media_attachments()
    {
        Carbon::setTestNow(now()->subDay(1)->startOfDay());
        $media = $this->createMedia();

        $pendingMedia = $media->markAsPending('draft-id');

        Carbon::setTestNow(null);

        (new PruneStaleMediaAttachments)();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        $this->assertDatabaseMissing('pending_media_attachments', ['id' => $pendingMedia->id]);
    }

    protected function createMedia()
    {
        $media = new Media();

        $media->forceFill([
            'disk' => 'local',
            'directory' => 'media',
            'filename' => 'filename',
            'extension' => 'jpg',
            'mime_type' => 'image/jpg',
            'size' => 200,
            'aggregate_type' => 'image',
        ]);

        $media->save();

        return $media;
    }
}
