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

trait ChecksForDuplicates
{
    /**
     * Duplicate checker data
     */
    protected array $checkDuplicatesWith = [];

    /**
     * Add duplicates checker data
     */
    public function checkPossibleDuplicatesWith(string $url, array $params, string $langKey): static
    {
        return $this->withMeta([
            'checkDuplicatesWith' => [
                'url' => $url,
                'params' => $params,
                'lang_keypath' => $langKey,
            ],
        ]);
    }
}
