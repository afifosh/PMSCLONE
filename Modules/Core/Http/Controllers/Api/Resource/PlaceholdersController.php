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

namespace Modules\Core\Http\Controllers\Api\Resource;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Resource\MailPlaceholders;

class PlaceholdersController extends ApiController
{
    /**
     * Retrieve placeholders via fields.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->response(MailPlaceholders::createGroupsFromResources(
            $request->input('resources', [])
        ));
    }

    /**
     * Parse the given content via the given resources records.
     */
    public function parse(Request $request): JsonResponse
    {
        $content = $request->content;

        collect($request->input('resources', []))->map(function ($resource) {
            $instance = Innoclapps::resourceByName($resource['name']);

            return $instance ? [
                'record' => $record = $instance->displayQuery()->find($resource['id']),
                'resource' => $instance,
                'placeholders' => new MailPlaceholders($instance, $record),
            ] : null;
        })->filter()
            ->reject(fn ($resource) => $request->user()->cant('view', $resource['record']))
            ->unique(fn ($resource) => $resource['resource']->name())
            ->each(function ($resource) use (&$content) {
                $content = $resource['placeholders']->parseWhenViaInputFields($content);
            });

        return $this->response($content);
    }
}
