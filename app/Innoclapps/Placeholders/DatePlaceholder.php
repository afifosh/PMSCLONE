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

use App\Innoclapps\Facades\Format;

class DatePlaceholder extends Placeholder
{
    /**
     * The placeholder tag
     *
     * @var string
     */
    public string $tag = 'date';

    /**
     * The user the date is intended for
     *
     * @var null|\App\Models\Admin
     */
    protected $user;

    /**
     * Custom formatter callback
     *
     * @var null|callable
     */
    protected $formatCallback;

    /**
     * Format the placeholder
     *
     * @param string|null $contentType
     *
     * @return string
     */
    public function format(?string $contentType = null)
    {
        if (is_callable($this->formatCallback)) {
            return call_user_func_array($this->formatCallback, [$this->value, $this->user]);
        }

        return Format::date($this->value, $this->user);
    }

    /**
     * Add custom format callback
     *
     * @param callable $callback
     *
     * @return static
     */
    public function formatUsing(callable $callback)
    {
        $this->formatCallback = $callback;

        return $this;
    }

    /**
     * The user the date is intended for
     *
     * @param \App\Models\Admin $user
     *
     * @return static
     */
    public function forUser($user)
    {
        $this->user = $user;

        return $this;
    }
}