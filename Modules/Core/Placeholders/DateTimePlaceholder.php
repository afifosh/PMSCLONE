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

use Modules\Core\Facades\Format;

class DateTimePlaceholder extends Placeholder
{
    /**
     * The placeholder tag
     */
    public string $tag = 'date';

    /**
     * The user the date is intended for
     *
     * @var null|\Modules\Users\Models\User
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
     * @return string
     */
    public function format(?string $contentType = null)
    {
        if (is_callable($this->formatCallback)) {
            return call_user_func_array($this->formatCallback, [$this->value, $this->user]);
        }

        return Format::dateTime($this->value, $this->user);
    }

    /**
     * Add custom format callback
     *
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
     * @param  \Modules\Users\Models\User  $user
     * @return null\Modules\Users\Models\User
     */
    public function forUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
