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

use App\Http\View\FrontendComposers\Template;
use Illuminate\Http\Request;
use Modules\Activities\Filters\ResourceActivitiesFilter;
use Modules\Activities\Filters\ResourceNextActivityDate as ResourceNextActivityDateFilter;
use Modules\Comments\Contracts\PipesComments;
use Modules\Contacts\Cards\ContactsByDay;
use Modules\Contacts\Cards\ContactsBySource;
use Modules\Contacts\Cards\RecentlyCreatedContacts;
use Modules\Contacts\Criteria\ViewAuthorizedContactsCriteria;
use Modules\Contacts\Filters\AddressOperandFilter;
use Modules\Contacts\Filters\SourceFilter;
use Modules\Contacts\Http\Resources\ContactResource;
use Modules\Contacts\Resource\Contact\Frontend\ViewComponent;
use Modules\Core\Actions\DeleteAction;
use Modules\Core\Contracts\Resources\AcceptsCustomFields;
use Modules\Core\Contracts\Resources\AcceptsUniqueCustomFields;
use Modules\Core\Contracts\Resources\Exportable;
use Modules\Core\Contracts\Resources\HasEmail;
use Modules\Core\Contracts\Resources\Importable;
use Modules\Core\Contracts\Resources\Mediable;
use Modules\Core\Contracts\Resources\Resourceful;
use Modules\Core\Contracts\Resources\Tableable;
use Modules\Core\Criteria\RequestCriteria;
use Modules\Core\Criteria\SearchByFirstNameAndLastNameCriteria;
use Modules\Core\Facades\Permissions;
use Modules\Core\Filters\DateTime as DateTimeFilter;
use Modules\Core\Filters\HasMany as HasManyFilter;
use Modules\Core\Filters\Operand;
use Modules\Core\Filters\Text as TextFilter;
use Modules\Core\Menu\MenuItem;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Resource\Import\Import;
use Modules\Core\Resource\Resource;
use Modules\Core\Table\Table;
use Modules\Deals\Filters\ResourceDealsFilter;
use Modules\Documents\Filters\ResourceDocumentsFilter;
use Modules\MailClient\Filters\ResourceEmailsFilter;
use Modules\Users\Filters\ResourceUserTeamFilter;
use Modules\Users\Filters\UserFilter;

class Contact extends Resource implements Resourceful, Tableable, Mediable, Importable, Exportable, HasEmail, AcceptsCustomFields, AcceptsUniqueCustomFields, PipesComments
{
    /**
     * Indicates whether the resource has Zapier hooks
     */
    public static bool $hasZapierHooks = true;

    /**
     * The column the records should be default ordered by when retrieving
     */
    public static string $orderBy = 'first_name';

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
    public static string $model = 'Modules\Contacts\Models\Contact';

    /**
     * Get the request criteria for the resource.
     */
    public function getRequestCriteria(ResourceRequest $request): RequestCriteria
    {
        $criteria = parent::getRequestCriteria($request);
        // When search_fields exists in request for the RequestCriteria
        // we will prevent using the SearchByFirstNameAndLastNameCriteria criteria
        // to avoid unnecessary and not-accurate searches
        if ($request->isSearching() && $request->missing('search_fields')) {
            $criteria->appends(fn ($query) => $query->orWhere(function ($query) {
                $query->criteria(SearchByFirstNameAndLastNameCriteria::class);
            }));
        }

        return $criteria;
    }

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
            MenuItem::make(static::label(), '/contacts', 'Users')
                ->position(25)
                ->inQuickCreate()
                ->keyboardShortcutChar('C'),
        ];
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName(): string
    {
        return 'contacts';
    }

    /**
     * Get the resource available cards
     */
    public function cards(): array
    {
        return [
            (new ContactsByDay)->refreshOnActionExecuted()->help(__('core::app.cards.creation_date_info')),
            (new ContactsBySource)->refreshOnActionExecuted()->help(__('core::app.cards.creation_date_info'))->color('info'),
            (new RecentlyCreatedContacts)->onlyOnDashboard(),
        ];
    }

    /**
     * Provide the resource table class
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function table($query, Request $request): Table
    {
        return new ContactTable($query, $request);
    }

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource(): string
    {
        return ContactResource::class;
    }

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria(): string
    {
        return ViewAuthorizedContactsCriteria::class;
    }

    /**
     * Provides the resource available CRUD fields
     */
    public function fields(Request $request): array
    {
        return (new ContactFields)($this, $request);
    }

    /**
     * Get the resource importable class
     */
    public function importable(): Import
    {
        return parent::importable()->lookupForDuplicatesUsing(function ($request) {
            if ($contact = $this->finder()->match(['email' => $request->email])) {
                return $contact;
            }

            if ($contact = $this->finder()->matchByPhone($request->phones)) {
                return $contact;
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
            TextFilter::make('first_name', __('contacts::fields.contacts.first_name'))->withoutEmptyOperators(),
            TextFilter::make('last_name', __('contacts::fields.contacts.last_name')),
            TextFilter::make('email', __('contacts::fields.contacts.email')),
            UserFilter::make(__('contacts::fields.contacts.user.name')),
            ResourceUserTeamFilter::make(__('users::team.owner_team')),
            DateTimeFilter::make('owner_assigned_date', __('contacts::fields.contacts.owner_assigned_date')),
            ResourceDocumentsFilter::make(),
            ResourceActivitiesFilter::make(),
            SourceFilter::make(),
            TextFilter::make('job_title', __('contacts::fields.contacts.job_title')),
            AddressOperandFilter::make('contacts'),
            UserFilter::make(__('core::app.created_by'), 'created_by')->withoutNullOperators()->canSeeWhen('view all contacts'),

            HasManyFilter::make('phones', __('contacts::fields.contacts.phone'))->setOperands([
                Operand::make('number', __('contacts::fields.contacts.phone'))->filter(TextFilter::class),
            ])->hideOperands(),

            ResourceDealsFilter::make(__('contacts::contact.contact')),
            ResourceEmailsFilter::make(),
            ResourceNextActivityDateFilter::make(),
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
                    fn ($request, $model) => $request->user()->can('bulk delete contacts')
                ),
        ];
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return __('contacts::contact.contacts');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return __('contacts::contact.contact');
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
                        'export contacts' => __('core::app.export.export'),
                    ],
                ]);
            });
        });
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
        if ($this->finder) {
            return $this->finder;
        }

        return $this->finder = (new RecordFinder($this->newModel()))->with('phones');
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
