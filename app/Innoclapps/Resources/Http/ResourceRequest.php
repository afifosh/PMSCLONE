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

namespace App\Innoclapps\Resources\Http;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
{
    use InteractsWithResources;

    /**
     * Resolve the resource json resource and create appropriate response
     *
     * @param mixed $data
     *
     * @return array
     */
    public function toResponse($data)
    {
        if (! $this->resource()->jsonResource()) {
            return $data;
        }

        $jsonResource = $this->resource()->createJsonResource($data);

        if ($data instanceof Model) {
            $jsonResource->withActions(
                $this->resource()->resolveActions($this)
            );
        }

        return $jsonResource->toResponse($this)->getData();
    }

    /**
     * Check whether the current request is for create
     *
     * @return boolean
     */
    public function isCreateRequest()
    {
        return $this->intent == 'create' || $this instanceof CreateResourceRequest;
    }

    /**
     * Check whether the current request is for update
     *
     * @return boolean
     */
    public function isUpdateRequest()
    {
        return $this->intent == 'update' || $this->intent === 'details' || $this instanceof UpdateResourceRequest;
    }

    /**
     * Check whether the current request is via resource
     *
     * @return boolean
     */
    public function viaResource()
    {
        return $this->has('via_resource');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
