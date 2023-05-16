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

class ActionButtonPlaceholder extends UrlPlaceholder
{
    /**
     * Indicates the starting interpolation
     */
    public string $interpolationStart = '{{{';

    /**
     * Indicates the ending interpolation
     */
    public string $interpolationEnd = '}}}';

    /**
     * The placeholder tag
     */
    public string $tag = 'action_button';

    /**
     * Format the placeholder
     *
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        // $text and $mustache are empty because of versions compatibility
        // previous this was {{{ action_button }}} only now it's {{#action_button}}Text{{/action_button}}
        return function ($text = '', $mustache = null) use ($contentType) {
            if ($contentType === 'text') {
                return parent::format();
            }

            return view('core::mail.action', [
                'url' => parent::format(),
                'text' => $text ?: __('core::mail_template.placeholders.view_record'),
            ])->render();
        };
    }

    /**
     * Boot the placeholder and set default values
     *
     * @return void
     */
    public function boot()
    {
        $this->description('Formatted action button.');
    }
}
