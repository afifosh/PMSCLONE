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

namespace Modules\Contacts\Resource\Company\Frontend;

use App\Http\View\FrontendComposers\Component;
use App\Http\View\FrontendComposers\HasSections;
use App\Http\View\FrontendComposers\HasTabs;
use App\Http\View\FrontendComposers\Section;
use App\Http\View\FrontendComposers\Tab;
use JsonSerializable;

class ViewComponent extends Component implements JsonSerializable
{
    use HasTabs, HasSections;

    /**
     * Create new ViewComponent instance.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Build the component data
     */
    public function build(): array
    {
        return [
            'tabs' => $this->orderedTabs(),
            'sections' => $this->mergeSections(
                settings('company_view_sections') ?: []
            ),
        ];
    }

    /**
     * Init the component data
     */
    protected function init(): void
    {
        static::registerTabs([
            Tab::make('timeline', 'timeline-tab')->panel('timeline-tab-panel')->order(10),
        ]);

        static::registerSections([
            Section::make('details', 'details-section')->heading(__('core::app.record_view.sections.details')),
            Section::make('deals', 'deals-section')->heading(__('deals::deal.deals')),
            Section::make('contacts', 'contacts-section')->heading(__('contacts::contact.contacts')),
            Section::make('media', 'media-section')->heading(__('core::app.attachments')),
            Section::make('child', 'child-companies-section')->heading(trans_choice('contacts::company.child', 2)),
        ]);
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return $this->build();
    }
}
