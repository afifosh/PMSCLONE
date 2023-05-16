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

namespace Modules\Core\Placeholders;

use Exception;
use Modules\Core\Contracts\Presentable;

class GenericPlaceholder extends Placeholder
{
    /**
     * Format the placeholder
     *
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        if ($this->value instanceof Presentable) {
            return $this->value->display_name;
        }

        return $this->value;
    }

    /**
     * Serialize the placeholder for the front end
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        if (! $data['tag']) {
            throw new Exception('"tag" not provided for generic placeholder.');
        }

        return $data;
    }
}
