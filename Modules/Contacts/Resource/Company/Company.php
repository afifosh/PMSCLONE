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

use App\Http\View\FrontendComposers\Template;
use Illuminate\Http\Request;
use Modules\Activities\Filters\ResourceActivitiesFilter;
use Modules\Activities\Filters\ResourceNextActivityDate as ResourceNextActivityDateFilter;
use Modules\Comments\Contracts\PipesComments;
use Modules\Contacts\Cards\CompaniesByDay;
use Modules\Contacts\Cards\CompaniesBySource;
use Modules\Contacts\Criteria\ViewAuthorizedCompaniesCriteria;
use Modules\Contacts\Filters\AddressOperandFilter;
use Modules\Contacts\Filters\SourceFilter;
use Modules\Contacts\Http\Resources\CompanyResource;
use Modules\Contacts\Models\Industry;
use Modules\Contacts\Resource\Company\Frontend\ViewComponent;
use Modules\Core\Actions\DeleteAction;
use Modules\Core\Contracts\Resources\AcceptsCustomFields;
use Modules\Core\Contracts\Resources\AcceptsUniqueCustomFields;
use Modules\Core\Contracts\Resources\Exportable;
use Modules\Core\Contracts\Resources\HasEmail;
use Modules\Core\Contracts\Resources\Importable;
use Modules\Core\Contracts\Resources\Mediable;
use Modules\Core\Contracts\Resources\Resourceful;
use Modules\Core\Contracts\Resources\Tableable;
use Modules\Core\Facades\Permissions;
use Modules\Core\Filters\DateTime as DateTimeFilter;
use Modules\Core\Filters\HasMany as HasManyFilter;
use Modules\Core\Filters\Operand;
use Modules\Core\Filters\Select as SelectFilter;
use Modules\Core\Filters\Text as TextFilter;
use Modules\Core\Menu\MenuItem;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Resource\Import\Import;
use Modules\Core\Resource\RecordFinder;
use Modules\Core\Resource\Resource;
use Modules\Core\Settings\SettingsMenuItem;
use Modules\Core\Table\Table;
use Modules\Deals\Filters\ResourceDealsFilter;
use Modules\Documents\Filters\ResourceDocumentsFilter;
use Modules\MailClient\Filters\ResourceEmailsFilter;
use Modules\Users\Filters\ResourceUserTeamFilter;
use Modules\Users\Filters\UserFilter;

class Company extends Resource implements Resourceful, Tableable, Mediable, Importable, Exportable, HasEmail, AcceptsCustomFields, AcceptsUniqueCustomFields, PipesComments
{
    /**
     * Indicates whether the resource has Zapier hooks
     */
    public static bool $hasZapierHooks = true;

    /**
     * The column the records should be default ordered by when retrieving
     */
    public static string $orderBy = 'name';

    /**
     * Indicates whether the resource is globally searchable
     */
    public static bool $globallySearchable = true;

    /**
     * Indicates whether the resource fields are customizeable
     */
    public static bool $fieldsCustomizable = true;

    /**
     * The model the resource is related to
     */
    public static string $model = 'Modules\Contacts\Models\Company';

    /**
     * Get the resource model email address field name
     */
    public function emailAddressField(): string
    {
        return 'email';
    }

    /**
     * Get the menu items for the resource
     */
    public function menu(): array
    {
        return [
            MenuItem::make(static::label(), '/companies', 'OfficeBuilding')
                ->position(30)
                ->inQuickCreate()
                ->keyboardShortcutChar('O'),
        ];
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName(): string
    {
        return 'companies';
    }

    /**
     * Get the resource available cards
     */
    public function cards(): array
    {
        return [
            (new CompaniesByDay)->refreshOnActionExecuted()->help(__('core::app.cards.creation_date_info')),
            (new CompaniesBySource)->refreshOnActionExecuted()->help(__('core::app.cards.creation_date_info'))->color('info'),
        ];
    }

    /**
     * Provide the resource table class
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function table($query, Request $request): Table
    {
        return new CompanyTable($query, $request);
    }

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource(): string
    {
        return CompanyResource::class;
    }

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria(): string
    {
        return ViewAuthorizedCompaniesCriteria::class;
    }

    /**
     * Provides the resource available CRUD fields
     */
    public function fields(Request $request): array
    {
        return (new CompanyFields)($this, $request);
    }

    /**
     * Get the resource importable class
     */
    public function importable(): Import
    {
        return parent::importable()->lookupForDuplicatesUsing(function ($request) {
            if ($company = $this->finder()->match(['email' => $request->email])) {
                return $company;
            }

            if ($company = $this->finder()->matchAll([
                'street' => $request->street,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country_id' => $request->country_id,
            ])) {
                return $company;
            }

            return null;
        });
    }

    /**
     * Get the resource available Filters
     */
    public function filters(ResourceRequest $request): array
    {
        return [
            TextFilter::make('companies.name', __('contacts::fields.companies.name'))->withoutEmptyOperators(),
            TextFilter::make('domain', __('contacts::fields.companies.domain')),
            TextFilter::make('email', __('contacts::fields.companies.email')),
            UserFilter::make(__('contacts::fields.companies.user.name')),
            ResourceUserTeamFilter::make(__('users::team.owner_team')),
            DateTimeFilter::make('owner_assigned_date', __('contacts::fields.companies.owner_assigned_date')),
            ResourceDocumentsFilter::make(),
            ResourceActivitiesFilter::make(),

            SelectFilter::make('industry_id', __('contacts::fields.companies.industry.name'))
                ->labelKey('name')
                ->valueKey('id')
                ->options(function () {
                    return Industry::get(['id', 'name']);
                }),

            SourceFilter::make(),
            AddressOperandFilter::make('companies'),

            HasManyFilter::make('phones', __('contacts::fields.companies.phone'))->setOperands([
                Operand::make('number', __('contacts::fields.companies.phone'))->filter(TextFilter::class),
            ])->hideOperands(),

            ResourceDealsFilter::make(__('contacts::company.company')),
            ResourceEmailsFilter::make(),
            ResourceNextActivityDateFilter::make(),
            UserFilter::make(__('core::app.created_by'), 'created_by')->withNullOperators()->canSeeWhen('view all companies'),
            DateTimeFilter::make('updated_at', __('core::app.updated_at')),
            DateTimeFilter::make('created_at', __('core::app.created_at')),
        ];
    }

    /**
     * Provides the resource available actions
     */
    public function actions(ResourceRequest $request): array
    {
        return [
            new \Modules\Core\Actions\SearchInGoogleAction,
            (new \Modules\Users\Actions\AssignOwnerAction)->onlyOnIndex(),

            (new DeleteAction)->useName(__('core::app.delete')),

            (new DeleteAction)->isBulk()
                ->useName(__('core::app.delete'))
                ->authorizedToRunWhen(
                    fn ($request, $model) => $request->user()->can('bulk delete companies')
                ),
        ];
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return __('contacts::company.companies');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return __('contacts::company.company');
    }

    /**
     * Register permissions for the resource
     */
    public function registerPermissions(): void
    {
        $this->registerCommonPermissions();

        Permissions::register(function ($manager) {
            $manager->group($this->name(), function ($manager) {
                $manager->view('export', [
                    'permissions' => [
                        'export companies' => __('core::app.export.export'),
                    ],
                ]);
            });
        });
    }

    /**
     * Register the settings menu items for the resource
     */
    public function settingsMenu(): array
    {
        return [
            SettingsMenuItem::make(__('contacts::company.companies'), '/settings/companies', 'OfficeBuilding')->order(24),
        ];
    }

    /**
     * Get the resource frontend template
     */
    public function frontendTemplate(): Template
    {
        return (new Template)->viewComponent(new ViewComponent);
    }

    /**
     * Get the duplicate finder instance
     */
    public function finder(): RecordFinder
    {
        return parent::finder()->with('phones');
    }

    /**
     * Serialize the resource
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'frontend' => $this->frontendTemplate(),
        ]);
    }
}
