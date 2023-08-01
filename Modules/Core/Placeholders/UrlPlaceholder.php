<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Placeholders;

use Modules\Core\Contracts\Presentable;

class UrlPlaceholder extends Placeholder
{
    /**
     * Initialize new UrlPlaceholder instance.
     *
     * @param  \Closure|mixed  $value
     */
    public function __construct($value = null, string $tag = 'url')
    {
        parent::__construct($tag, $value);

        $this->description('URL');
    }

    /**
     * Format the placeholder
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        return url(
            $this->value instanceof Presentable ? $this->value->path : $this->value
        );
    }
}
