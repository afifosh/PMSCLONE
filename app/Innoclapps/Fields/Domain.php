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

namespace App\Innoclapps\Fields;

use App\Innoclapps\Resources\Http\ResourceRequest;

class Domain extends Field
{
    /**
     * Field component
     */
    public ?string $component = 'domain-field';

    /**
     * This field support input group
     *
     * @var boolean
     */
    public bool $supportsInputGroup = true;

    /**
     * Boot field
     *
     * Sets icon
     *
     * @return null
     */
    public function boot()
    {
        $this->provideSampleValueUsing(fn () => 'example.com')->prependIcon('Globe');
    }

    /**
     * Get the field value for the given request
     *
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $request
     * @param string $requestAttribute
     *
     * @return mixed
     */
    public function attributeFromRequest(ResourceRequest $request, $requestAttribute) : mixed
    {
        $value = parent::attributeFromRequest($request, $requestAttribute);

        return \App\Innoclapps\Domain::extractFromUrl($value ?? '');
    }
}