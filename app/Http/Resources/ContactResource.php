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

namespace App\Http\Resources;

use App\Innoclapps\Resources\Http\JsonResource;

/** @mixin \App\Models\Contact */
class ContactResource extends JsonResource
{
    use ProvidesCommonData;

    /**
     * Transform the resource collection into an array.
     *
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $request
     *
     * @return array
     */
    public function toArray($request)
    {

        return $this->withCommonData([

            'notes_count' => $this->whenCounted('notes', fn () => (int) $this->notes_count),
            'calls_count' => $this->whenCounted('calls', fn () => (int) $this->calls_count),

            $this->mergeWhen(! $request->isZapier(), [
                'avatar'              => $this->avatar,
                'avatar_url'          => $this->avatar_url,
                'uploaded_avatar_url' => $this->uploaded_avatar_url,
            ]),

        ], $request);
    }
}