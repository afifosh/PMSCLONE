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

use App\Innoclapps\JsonResource;

/** @mixin \App\Innoclapps\Models\OAuthAccount */
class OAuthAccountResource extends JsonResource
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
            'user_id'       => $this->user_id,
            'type'          => $this->type,
            'email'         => $this->email,
            'requires_auth' => $this->requires_auth,
        ], $request);
    }
}