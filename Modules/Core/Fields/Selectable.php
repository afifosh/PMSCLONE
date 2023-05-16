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

namespace Modules\Core\Fields;

trait Selectable
{
    /**
     * Set async URL for searching
     */
    public function async(string $asyncUrl): static
    {
        return $this->withMeta([
            'asyncUrl' => $asyncUrl,
            // Automatically add placeholder "Type to search..." on async fields
            'attributes' => ['placeholder' => __('core::app.type_to_search')],
        ]);
    }

    /**
     * Set the URL to lazy load options when the field is first opened
     */
    public function lazyLoad(string $url, array $params = []): static
    {
        return $this->withMeta(['lazyLoad' => [
            'url' => $url,
            'params' => $params,
        ]]);
    }
}
