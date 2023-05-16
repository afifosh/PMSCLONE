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

namespace Modules\Core\Highlights;

use Illuminate\Support\Arr;

class Highlights
{
    protected static array $highlights = [];

    /**
     * Get all the highlights
     */
    public static function get(): array
    {
        return array_values(static::$highlights);
    }

    /**
     * Register new highlight
     */
    public static function register(Highlight|array $highlight): static
    {
        foreach (Arr::wrap($highlight) as $highlight) {
            if (! $highlight instanceof Highlight) {
                $highlight = new $highlight;
            }

            static::$highlights[$highlight->name()] = $highlight;
        }

        return new static;
    }
}
