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

namespace Modules\Core\Tests\Feature\Updater;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\App;
use Modules\Core\Updater\Patcher;
use Tests\TestCase;

/**
 * @group updater
 */
class UpdateDownloadControllerTest extends TestCase
{
    use TestsUpdater;

    public function test_a_patch_can_be_downloaded()
    {
        $this->signIn();

        $token = '96671235-ddb3-40ab-8ab9-3ca5df8de6b7';

        $response = json_encode([
            [
                'date' => '2021-08-24T18:52:54.000000Z',
                'description' => 'Fixes issue with activities',
                'token' => $token,
                'version' => '1.0.0',
            ],
        ]);

        App::singleton(Patcher::class, function () use ($response) {
            return $this->createPatcherInstance([
                new Response(200, [], $response),
                new Response(200, [], $response),
            ]);
        });

        $this->getJson('/patches/'.$token)->assertDownload('v1.0.0-'.$token.'.zip');
    }
}
