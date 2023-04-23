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

namespace App\Innoclapps\Translation;

use JsonSerializable;
use Illuminate\Support\Arr;

class DotNotationResult implements JsonSerializable
{
    /**
     * Initialize DotNotation
     *
     * @param array $translations
     */
    public function __construct(protected array $translations)
    {
    }

    /**
     * Get the translations groups with dot notation
     *
     * @return array
     */
    public function groups() : array
    {
        return collect($this->translations)->mapWithKeys(function ($translations, $group) {
            return [$group => Arr::dot($translations)];
        })->all();
    }

    /**
     * Get clean array from group dot notation array
     *
     * @return array
     */
    public function clean() : array
    {
        $array = [];

        foreach ($this->translations as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->groups();
    }
}
