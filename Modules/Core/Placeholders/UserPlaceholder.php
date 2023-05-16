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

class UserPlaceholder extends Placeholder
{
    /**
     * The placeholder tag
     */
    public string $tag = 'user';

    public string $labelKey = 'name';

    /**
     * Format the placeholder
     *
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        return is_a($this->value, \Modules\Users\Models\User::class) ?
            $this->value->{$this->labelKey} :
            $this->value;
    }

    /**
     * Set the user label key
     */
    public function labelKey(string $key): static
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
        $this->description(__('users::user.user'));
    }
}
