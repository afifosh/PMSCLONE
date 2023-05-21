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

namespace Modules\Contacts\Resource\Company;

use Modules\Activities\Fields\NextActivityDate;
use Modules\Contacts\Fields\Company;
use Modules\Contacts\Fields\Contacts;
use Modules\Contacts\Fields\Phone;
use Modules\Contacts\Fields\Source;
use Modules\Contacts\Http\Resources\IndustryResource;
use Modules\Contacts\Models\Company as CompanyModel;
use Modules\Contacts\Models\Industry;
use Modules\Core\CountryCallingCode;
use Modules\Core\Facades\Fields;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Fields\BelongsTo;
use Modules\Core\Fields\Country;
use Modules\Core\Fields\DateTime;
use Modules\Core\Fields\Domain;
use Modules\Core\Fields\Email;
use Modules\Core\Fields\IntroductionField;
use Modules\Core\Fields\MorphToMany;
use Modules\Core\Fields\Text;
use Modules\Core\Fields\User;
use Modules\Deals\Fields\Deals;
use Modules\Notes\Fields\ImportNote;

class CompanyFields
{
    /**
     * Provides the company resource available fields
     *
     * @param  \Modules\Core\Resource\Resource  $resource
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __invoke($resource, $request)
    {
        return [
            Text::make('name', __('contacts::fields.companies.name'))
                ->tapIndexColumn(fn ($column) => $column->width('340px')->minWidth('340px'))
                ->checkPossibleDuplicatesWith('/companies/search', ['field' => 'name'], 'contacts::company.possible_duplicate')
                ->creationRules(['required', 'string'])
                ->updateRules(['filled', 'string'])
                ->rules('max:191')
                ->required(true)
                ->hideFromDetail()
                ->excludeFromSettings(Fields::DETAIL_VIEW)
                ->primary(),

            Domain::make('domain', __('contacts::fields.companies.domain'))
                ->rules(['nullable', 'string', 'max:191'])
                ->hideFromIndex(),

            Email::make('email', __('contacts::fields.companies.email'))
                ->rules('max:191')
                ->unique(CompanyModel::class)
                ->validationMessages([
                    'unique' => __('contacts::company.validation.email.unique'),
                ]),

            BelongsTo::make('industry', Industry::class, __('contacts::fields.companies.industry.name'))
                ->setJsonResource(IndustryResource::class)
                ->options(Innoclapps::resourceByModel(Industry::class))
                ->acceptLabelAsValue()
                ->hidden(),

            Phone::make('phones', __('contacts::fields.companies.phone'))
                ->checkPossibleDuplicatesWith('/companies/search', ['field' => 'phones'], 'contacts::company.possible_duplicate')
                ->requireCallingPrefix(
                    function () use ($resource) {
                        if ((bool) settings('require_calling_prefix_on_phones')) {
                            return $resource->resource?->country_id ?? CountryCallingCode::guess() ?? true;
                        }
                    }
                )->hidden(),

            Contacts::make()
                ->label(__('contacts::contact.total'))
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

            Source::make()
                ->collapsed()
                ->hideWhenCreating(),

            Company::make('parent', __('contacts::fields.companies.parent.name'), 'parent_company_id')
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->excludeFromImport(),

            Text::make('street', __('contacts::fields.companies.street'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->rules(['nullable', 'string', 'max:191']),

            Text::make('city', __('contacts::fields.companies.city'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->rules(['nullable', 'string', 'max:191']),

            Text::make('state', __('contacts::fields.companies.state'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->rules(['nullable', 'string', 'max:191']),

            Text::make('postal_code', __('contacts::fields.companies.postal_code'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->rules(['nullable', 'max:191']),

            Country::make(__('contacts::fields.companies.country.name'))
                ->collapsed()
                ->hideFromIndex()
                ->hideWhenCreating(),

            User::make(__('contacts::fields.companies.user.name'))
                ->primary() // Primary field to show the owner in the form
                ->acceptLabelAsValue(false)
                ->withMeta(['attributes' => ['placeholder' => __('core::app.no_owner')]])
                ->tapIndexColumn(function ($column) {
                    $column->primary(false)
                        ->select('avatar')
                        ->appends('avatar_url')
                        ->useComponent('table-data-avatar-column');
                })
                ->notification(\Modules\Contacts\Notifications\UserAssignedToCompany::class)
                ->trackChangeDate('owner_assigned_date')
                ->hideFromDetail()
                ->excludeFromSettings(Fields::DETAIL_VIEW)
                ->showValueWhenUnauthorizedToView(),

            IntroductionField::make(__('core::resource.associate_with_records'))
                ->strictlyForCreation()
                ->titleIcon('Link')
                ->order(1000),

            // Deals::make()
            //     ->excludeFromSettings()
            //     ->strictlyForCreationAndIndex()
            //     ->hideFromIndex()
            //     ->order(1001),

            Contacts::make()
                ->excludeFromSettings()
                ->strictlyForCreationAndIndex()
                ->hideFromIndex()
                ->order(1002),

            DateTime::make('owner_assigned_date', __('contacts::fields.companies.owner_assigned_date'))
                ->exceptOnForms()
                ->hidden(),

            NextActivityDate::make(),

            // ImportNote::make(),

            DateTime::make('updated_at', __('core::app.updated_at'))
                ->excludeFromImportSample()
                ->strictlyForIndex()
                ->hidden(),

            DateTime::make('created_at', __('core::app.created_at'))
                ->excludeFromImportSample()
                ->strictlyForIndex(),
        ];
    }
}
