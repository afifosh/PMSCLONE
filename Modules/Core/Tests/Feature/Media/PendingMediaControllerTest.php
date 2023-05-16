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

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendingMediaControllerTest extends TestCase
{
    public function test_pending_media_can_be_stored()
    {
        $this->signIn();
        config()->set('mediable.allowed_extensions', ['jpg']);
        Storage::fake(config('mediable.default_disk'));

        $this->postJson('/api/media/pending/testDraftId', [
            'file' => UploadedFile::fake()->image('photo1.jpg'),
        ])->assertJson([
            'file_name' => 'photo1.jpg',
            'extension' => 'jpg',
            'disk_path' => 'pending-attachments/photo1.jpg',
            'was_recently_created' => true,
            'pending_data' => ['draft_id' => 'testDraftId'],
        ]);
    }

    public function test_pending_media_can_be_deleted()
    {
        $this->signIn();
        config()->set('mediable.allowed_extensions', ['jpg']);
        Storage::fake(config('mediable.default_disk'));

        $id = $this->postJson('/api/media/pending/testDraftId', [
            'file' => UploadedFile::fake()->image('photo1.jpg'),
        ])->getData()->id;

        $this->deleteJson('/api/media/pending/'.$id)->assertNoContent();
        $this->assertDatabaseCount('pending_media_attachments', 0);
    }
}
