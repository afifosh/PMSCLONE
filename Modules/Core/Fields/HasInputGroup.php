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

namespace Modules\Core\Fields;

trait HasInputGroup
{
    /**
     * A custom icon to be incorporated in input group
     */
    public ?string $icon = null;

    /**
     * Input group append - right
     *
     * @param  string  $value
     */
    public function inputGroupAppend($value): static
    {
        if ($this->supportsInputGroup()) {
            $this->withMeta(['inputGroupAppend' => $value]);
        }

        return $this;
    }

    /**
     * Input group prepend - left
     *
     * @param  string  $value
     */
    public function inputGroupPrepend($value): static
    {
        if ($this->supportsInputGroup()) {
            $this->withMeta(['inputGroupPrepend' => $value]);
        }

        return $this;
    }

    /**
     * Checks whether the field support input group
     */
    public function supportsInputGroup(): bool
    {
        return property_exists($this, 'supportsInputGroup') && (bool) $this->supportsInputGroup;
    }

    /**
     * Append icon to the field
     */
    public function appendIcon(string $icon): static
    {
        return $this->icon($icon, true);
    }

    /**
     * Prepend icon to the field
     */
    public function prependIcon(string $icon): static
    {
        return $this->icon($icon, false);
    }

    /**
     * Custom input group icon
     *
     * @param  string  $icon icon name
     * @param  bool  $append whether to append or prepend the icon
     */
    public function icon(string $icon, bool $append = true): static
    {
        if ($this->supportsInputGroup()) {
            $this->icon = $icon;
            $method = $append ? 'inputGroupAppend' : 'inputGroupPrepend';

            $this->{$method}(true);
        }

        return $this;
    }
}
