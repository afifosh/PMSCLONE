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

namespace App\Innoclapps\Placeholders;

use App\Innoclapps\Facades\Innoclapps;

class UserPlaceholder extends Placeholder
{
    /**
     * The placeholder tag
     *
     * @var string
     */
    public string $tag = 'user';

    /**
     * @var string
     */
    public string $labelKey = 'name';

    /**
     * Format the placeholder
     *
     * @param string|null $contentType
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        return is_a($this->value, Innoclapps::getUserRepository()->model()) ?
            $this->value->{$this->labelKey} :
            $this->value;
    }

    /**
     * Set the user label key
     *
     * @param string $key
     *
     * @return static
     */
    public function labelKey(string $key) : static
    {
        $this->labelKey = $key;

        return $this;
    }

    /**
     * Boot the placeholder and set default values
     *
     * @return void
     */
    public function boot()
    {
        $this->description(__('user.user'));
    }
}