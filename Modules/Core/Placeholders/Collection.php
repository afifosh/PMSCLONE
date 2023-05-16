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

use JsonSerializable;
use Mustache_Engine;

class Collection implements JsonSerializable
{
    protected array $parsed = [];

    /**
     * Create new Collection instance.
     */
    public function __construct(protected array $placeholders)
    {
    }

    /**
     * Forget placeholders from the collection
     */
    public function forget(string|array $tagName): static
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
     */
    public function push(Placeholder|array $placeholders): static
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
     */
    public function parse(?string $contentType = 'html'): array
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
     * @param  string  $template
     * @param  array  $placeholders
     * @return string
     */
    public function render($template, array $placeholders = null)
    {
        return (new Mustache_Engine())->render($template, $placeholders ?? $this->parse());
    }

    /**
     * Perform formatting on the placeholders
     */
    protected function performFormatting(?string $contentType): array
    {
        return collect($this->placeholders)->mapWithKeys(
            fn ($placeholder) => [$placeholder->tag => $placeholder->format($contentType)]
        )->all();
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return $this->placeholders;
    }
}
