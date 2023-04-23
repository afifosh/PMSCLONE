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

/** @mixin \App\Models\PredefinedMailTemplate */
class PredefinedMailTemplateResource extends JsonResource
{
    use ProvidesCommonData;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->withCommonData([
            'name'      => $this->name,
            'subject'   => $this->subject,
            'body'      => $this->body,
            'is_shared' => $this->is_shared,
            'user_id'   => $this->user_id,
            'user'      => new UserResource($this->whenLoaded('user')),
        ], $request);
    }
}