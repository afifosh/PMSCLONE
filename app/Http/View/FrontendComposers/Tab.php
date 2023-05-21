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

class Tab
{
    use Makeable;

    public ?int $displayOrder = null;

    /**
     * Create new Tab instance.
     */
    public function __construct(public string $id, public string $component, public ?string $panelComponent = null)
    {
    }

    /**
     * Add the tab order
     */
    public function order(int $order): static
    {
        $this->displayOrder = $order;

        return $this;
    }

    /**
     * Set the tab panel component
     */
    public function panel(string $component): static
    {
        $this->panelComponent = $component;

        return $this;
    }
}
