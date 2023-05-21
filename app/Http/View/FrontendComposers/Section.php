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

namespace App\Http\View\FrontendComposers;

use Modules\Core\Makeable;

class Section
{
    use Makeable;

    /**
     * Indicates whether the section is enabled
     */
    public bool $enabled = true;

    /**
     * Section order
     */
    public ?int $order = null;

    /**
     * Section heading
     */
    public ?string $heading = null;

    /**
     * Create new Section instance
     */
    public function __construct(public string $id, public string $component)
    {
    }

    /**
     * Set the section heading.
     */
    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }
}
