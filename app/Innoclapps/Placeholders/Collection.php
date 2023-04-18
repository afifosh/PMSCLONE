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

use Mustache_Engine;
use JsonSerializable;

class Collection implements JsonSerializable
{
    protected array $parsed = [];

    /**
     * Create new Collection instance
     *
     * @param array $placeholders
     */
    public function __construct(protected array $placeholders)
    {
    }

    /**
     * Forget placeholders from the collection
     *
     * @param string|array $tagName
     *
     * @return static
     */
    public function forget(string|array $tagName)
    {
        $this->placeholders = collect($this->placeholders)
            ->reject(
                fn ($placeholder) => in_array($placeholder->tag, (array) $tagName)
            )->values()->all();

        $this->parsed = [];

        return $this;
    }

    /**
     * Push placeholders
     *
     * @param \App\Innoclapps\Placeholders\Placeholder|array $placeholders
     *
     * @return static
     */
    public function push($placeholders)
    {
        $this->placeholders = array_merge(
            $this->placeholders,
            is_array($placeholders) ? $placeholders : func_get_args()
        );

        $this->parsed = [];

        return $this;
    }

    /**
     * Parse all the placeholders with their formatted values
     *
     * @param string|null $contentType
     *
     * @return array
     */
    public function parse(?string $contentType = 'html')
    {
        $cacheKey = $contentType ?? 'general';

        if (! array_key_exists($cacheKey, $this->parsed)) {
            $this->parsed[$cacheKey] = $this->performFormatting($contentType);
        }

        return $this->parsed[$cacheKey];
    }

    /**
     * Replace the placeholders to the given template
     *
     * @param string $template
     * @param array $placeholders
     *
     * @return string
     */
    public function render($template, array $placeholders = null)
    {
        return (new Mustache_Engine())->render($template, $placeholders ?? $this->parse());
    }

    /**
     * Perform formatting on the placeholders
     *
     * @param string|null $contentType
     *
     * @return array
     */
    protected function performFormatting(?string $contentType) : array
    {
        return collect($this->placeholders)->mapWithKeys(
            fn ($placeholder) => [$placeholder->tag => $placeholder->format($contentType)]
        )->all();
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->placeholders;
    }
}
