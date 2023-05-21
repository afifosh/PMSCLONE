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

namespace Modules\Contacts\Resource\Contact;

use Modules\Activities\Fields\NextActivityDate;
use Modules\Contacts\Fields\Companies;
use Modules\Contacts\Fields\Phone;
use Modules\Contacts\Fields\Source;
use Modules\Contacts\Models\Contact;
use Modules\Core\CountryCallingCode;
use Modules\Core\Facades\Fields;
use Modules\Core\Fields\Country;
use Modules\Core\Fields\DateTime;
use Modules\Core\Fields\Email;
use Modules\Core\Fields\IntroductionField;
use Modules\Core\Fields\MorphToMany;
use Modules\Core\Fields\Text;
use Modules\Core\Fields\User;
use Modules\Deals\Fields\Deals;
use Modules\Notes\Fields\ImportNote;

class ContactFields
{
    /**
     * Provides the contact resource available fields
     *
     * @param  \Modules\Core\Resource\Resource  $resource
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __invoke($resource, $request)
    {
        return [
            Text::make('display_name', __('contacts::contact.contact'))
                ->primary()
                ->excludeFromExport()
                ->excludeFromImport()
                ->excludeFromZapierResponse()
                ->strictlyForIndex()
                ->tapIndexColumn(function ($column) {
                    $column->width('340px')
                        ->minWidth('340px')
                        ->queryAs(Contact::nameQueryExpression('display_name'));
                }),

            Text::make('first_name', __('contacts::fields.contacts.first_name'))
                ->primary()
                ->creationRules(['required', 'string'])
                ->updateRules(['filled', 'string'])
                ->hideFromDetail()
                ->excludeFromSettings(Fields::DETAIL_VIEW)
                ->excludeFromIndex()
                ->rules('max:191')
                ->required(true),

            Text::make('last_name', __('contacts::fields.contacts.last_name'))
                ->rules(['nullable', 'string', 'max:191'])
                ->hideFromDetail()
                ->excludeFromSettings(Fields::DETAIL_VIEW)
                ->excludeFromIndex(),

            Email::make('email', __('contacts::fields.contacts.email'))
                ->rules(['nullable', 'email', 'max:191'])
                ->unique(Contact::class)
                ->unique(\Modules\Users\Models\User::class)
                ->validationMessages(['unique' => __('contacts::contact.validation.email.unique')])
                ->showValueWhenUnauthorizedToView()
                ->tapIndexColumn(function ($column) {
                    $column->queryWhenHidden();
                }),

            Phone::make('phones', __('contacts::fields.contacts.phone'))
                ->checkPossibleDuplicatesWith('/contacts/search', ['field' => 'phones'], 'contacts::contact.possible_duplicate')
                ->canUnmarkUnique()
                ->unique(\Modules\Contacts\Models\Contact::class, __('contacts::contact.validation.phone.unique'))
                ->requireCallingPrefix(
                    function () use ($resource) {
                        if ((bool) settings('require_calling_prefix_on_phones')) {
                            return $resource->resource?->country_id ?? CountryCallingCode::guess() ?? true;
                        }
                    }
                ),

            User::make(__('contacts::fields.contacts.user.name'))
                ->primary()
                ->acceptLabelAsValue(false)
                ->withMeta(['attributes' => ['placeholder' => __('core::app.no_owner')]])
                ->notification(\Modules\Contacts\Notifications\UserAssignedToContact::class)
                ->trackChangeDate('owner_assigned_date')
                ->tapIndexColumn(function ($column) {
                    $column->primary(false)
                        ->select('avatar')
                        ->appends('avatar_url')
                        ->useComponent('table-data-avatar-column');
                })
                ->hideFromDetail()
                ->excludeFromSettings(Fields::DETAIL_VIEW)
                ->showValueWhenUnauthorizedToView(),

            Source::make(),

            IntroductionField::make(__('core::resource.associate_with_records'))
                ->strictlyForCreation()
                ->titleIcon('Link')
                ->order(1000),

            Companies::make()
                ->excludeFromSettings()
                ->strictlyForCreationAndIndex()
                ->hideFromIndex()
                ->order(1001),

            // Deals::make()
            //     ->excludeFromSettings()
            //     ->strictlyForCreationAndIndex()
            //     ->hideFromIndex()
            //     ->order(1002),

            DateTime::make('owner_assigned_date', __('contacts::fields.contacts.owner_assigned_date'))
                ->strictlyForIndex()
                ->excludeFromImport()
                ->hidden(),

            Companies::make()
                ->label(__('contacts::company.total'))
                ->exceptOnForms()
                ->hidden()
                ->count(),

            // Deals::make()
            //     ->label(__('deals::deal.total'))
            //     ->exceptOnForms()
            //     ->hidden()
            //     ->count(),

            // Deals::make('authorizedOpenDeals')
            //     ->label(__('deals::deal.open_deals'))
            //     ->exceptOnForms()
            //     ->excludeFromZapierResponse()
            //     ->hidden()
            //     ->count(),

            // Deals::make('authorizedClosedDeals')
            //     ->label(__('deals::deal.closed_deals'))
            //     ->exceptOnForms()
            //     ->excludeFromZapierResponse()
            //     ->hidden()
            //     ->count(),

            // Deals::make('authorizedWonDeals')
            //     ->label(__('deals::deal.won_deals'))
            //     ->exceptOnForms()
            //     ->excludeFromZapierResponse()
            //     ->hidden()
            //     ->count(),

            // Deals::make('authorizedLostDeals')
            //     ->label(__('deals::deal.lost_deals'))
            //     ->exceptOnForms()
            //     ->excludeFromZapierResponse()
            //     ->hidden()
            //     ->count(),

            MorphToMany::make('unreadEmailsForUser', __('mailclient::inbox.unread_count'))
                ->exceptOnForms()
                ->excludeFromZapierResponse()
                ->hidden()
                ->count(),

            MorphToMany::make('incompleteActivitiesForUser', __('activities::activity.incomplete_activities'))
                ->exceptOnForms()
                ->excludeFromZapierResponse()
                ->hidden()
                ->count(),

            MorphToMany::make('documentsForUser', __('documents::document.total_documents'))
                ->exceptOnForms()
                ->excludeFromZapierResponse()
                ->hidden()
                ->count(),

            MorphToMany::make('draftDocumentsForUser', __('documents::document.total_draft_documents'))
                ->exceptOnForms()
                ->excludeFromZapierResponse()
                ->hidden()
                ->count(),

            MorphToMany::make('calls', __('calls::call.total_calls'))
                ->exceptOnForms()
                ->excludeFromZapierResponse()
                ->hidden()
                ->count(),

            Text::make('job_title', __('contacts::fields.contacts.job_title'))
                ->rules(['nullable', 'string', 'max:191'])
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            Text::make('street', __('contacts::fields.contacts.street'))
                ->rules(['nullable', 'string', 'max:191'])
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            Text::make('city', __('contacts::fields.contacts.city'))
                ->rules(['nullable', 'string', 'max:191'])
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            Text::make('state', __('contacts::fields.contacts.state'))
                ->rules(['nullable', 'string', 'max:191'])
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            Text::make('postal_code', __('contacts::fields.contacts.postal_code'))
                ->rules(['nullable', 'max:191'])
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            Country::make(__('contacts::fields.contacts.country.name'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            NextActivityDate::make(),

            // ImportNote::make(),

            DateTime::make('updated_at', __('core::app.updated_at'))
                ->excludeFromImportSample()
                ->strictlyForIndex()
                ->hidden(),

            DateTime::make('created_at', __('core::app.created_at'))
                ->excludeFromImportSample()
                ->strictlyForIndex()
                ->hidden(),
        ];
    }
}
