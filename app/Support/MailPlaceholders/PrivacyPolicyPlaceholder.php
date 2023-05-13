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

namespace App\Support\MailPlaceholders;

use App\Innoclapps\Placeholders\UrlPlaceholder;

class PrivacyPolicyPlaceholder extends UrlPlaceholder
{
    /**
     * The placeholder tag
     *
     * @var string
     */
    public string $tag = 'privacy_policy';

    /**
     * Format the placeholder
     *
     * @param string|null $contentType
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        return privacy_url();
    }

    /**
     * Boot the placeholder and set default values
     *
     * @return void
     */
    public function boot()
    {
        $this->description(__('app.privacy_policy'));
    }
}