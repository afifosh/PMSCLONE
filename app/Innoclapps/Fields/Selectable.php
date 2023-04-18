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

trait Selectable
{
    /**
     * Set async URL for searching
     */
    public function async(string $asyncUrl) : static
    {
        $this->withMeta(['asyncUrl' => $asyncUrl]);

        // Automatically add placeholder "Type to search..." on async fields
        $this->withMeta(['attributes' => ['placeholder' => __('app.type_to_search')]]);

        return $this;
    }
}