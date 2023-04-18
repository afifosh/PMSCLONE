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

namespace App\Resources\Contact;

use Illuminate\Http\Request;
use App\Innoclapps\Table\Table;
use App\Innoclapps\Models\Model;
use App\Innoclapps\Menu\MenuItem;
use App\Innoclapps\Filters\Operand;
use App\Innoclapps\Resources\Resource;
use App\Http\Resources\ContactResource;
use App\Innoclapps\Facades\Permissions;
use App\Resources\Actions\DeleteAction;
use App\Innoclapps\Resources\Import\Import;
use App\Support\Filters\AddressOperandFilter;
use App\Innoclapps\Filters\Text as TextFilter;
use App\Support\Filters\ResourceUserTeamFilter;
use App\Innoclapps\Contracts\Resources\HasEmail;
use App\Innoclapps\Contracts\Resources\Mediable;
use App\Support\Filters\ResourceDocumentsFilter;
use App\Contracts\Repositories\ContactRepository;
use App\Innoclapps\Contracts\Resources\Tableable;
use App\Resources\Contact\Frontend\ViewComponent;
use App\Innoclapps\Contracts\Resources\Exportable;
use App\Innoclapps\Contracts\Resources\Importable;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Resources\User\Filters\User as UserFilter;
use App\Innoclapps\Contracts\Resources\Resourceful;
use App\Innoclapps\Filters\HasMany as HasManyFilter;
use App\Resources\Inbox\Filters\ResourceEmailsFilter;
use App\Innoclapps\Filters\DateTime as DateTimeFilter;
use App\Criteria\Contact\ViewAuthorizedContactsCriteria;
use App\Resources\Source\Filters\Source as SourceFilter;
use App\Innoclapps\Contracts\Resources\AcceptsCustomFields;
use App\Resources\Activity\Filters\ResourceActivitiesFilter;
use App\Innoclapps\Contracts\Resources\AcceptsUniqueCustomFields;
use App\Innoclapps\Contracts\Resources\ResourcefulRequestHandler;
use App\Innoclapps\Criteria\SearchByFirstNameAndLastNameCriteria;
use App\Resources\Activity\Filters\ResourceNextActivityDate as ResourceNextActivityDateFilter;

class Contact extends Resource implements Resourceful, Tableable, Mediable, Importable, Exportable, HasEmail, AcceptsCustomFields, AcceptsUniqueCustomFields
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
    public static string $model = 'App\Models\Contact';

    /**
    * Get the underlying resource repository
    *
    * @return \App\Innoclapps\Repository\AppRepository
    */
    public static function repository()
    {
        return tap(resolve(ContactRepository::class), function ($repository) {
            // When search_fields exists in request for the RequestCriteria
            // we will prevent using the SearchByFirstNameAndLastNameCriteria criteria
            // to avoid unnecessary and not-accurate searches
            if (request()->isSearching() && request()->missing('search_fields')) {
                $repository->appendToRequestCriteria(new SearchByFirstNameAndLastNameCriteria);
            }
        });
    }

    /**
    * Get the resource model email address field name
    */
    public function emailAddressField() : string
    {
        return 'email';
    }

    /**
    * Get the menu items for the resource
    */
    public function menu() : array
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
    public function associateableName() : string
    {
        return 'contacts';
    }


    /**
    * Provide the resource table class
    *
    * @param \App\Innoclapps\Repository\BaseRepository $repository
    */
    public function table($repository, Request $request) : Table
    {
        $repository->appendToRequestCriteria(new SearchByFirstNameAndLastNameCriteria);

        return new ContactTable($repository, $request);
    }

    /**
    * Get the json resource that should be used for json response
    */
    public function jsonResource() : string
    {
        return ContactResource::class;
    }

    /**
    * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
    */
    public function viewAuthorizedRecordsCriteria() : string
    {
        return ViewAuthorizedContactsCriteria::class;
    }

    /**
    * Provides the resource available CRUD fields
    */
    public function fields(Request $request) : array
    {
        return (new ContactFields)($this, $request);
    }

    /**
    * Get the resourceful CRUD handler class
    *
    * @param \App\Innoclapps\Repository\AppRepository|null $repository
    */
    public function resourcefulHandler(ResourceRequest $request, $repository = null) : ResourcefulRequestHandler
    {
        $repository ??= static::repository();

        return new ResourcefulHandler($request, $repository);
    }

    /**
     * Get the resource importable class
     */
    public function importable() : Import
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
    public function filters(ResourceRequest $request) : array
    {
        return [
            TextFilter::make('first_name', __('fields.contacts.first_name'))->withoutEmptyOperators(),

            TextFilter::make('last_name', __('fields.contacts.last_name')),

            TextFilter::make('email', __('fields.contacts.email')),



            DateTimeFilter::make('owner_assigned_date', __('fields.contacts.owner_assigned_date')),


            TextFilter::make('job_title', __('fields.contacts.job_title')),



            HasManyFilter::make('phones', __('fields.contacts.phone'))->setOperands([
                Operand::make('number', __('fields.contacts.phone'))->filter(TextFilter::class),
            ])->hideOperands(),


            ResourceEmailsFilter::make(),


            DateTimeFilter::make('updated_at', __('app.updated_at')),

            DateTimeFilter::make('created_at', __('app.created_at')),
        ];
    }

    /**
    * Provides the resource available actions
    */
    public function actions(ResourceRequest $request) : array
    {
        return [
            new \App\Resources\Actions\SearchInGoogleAction,
            (new \App\Resources\Actions\AssignOwnerAction)->onlyOnIndex(),

            (new DeleteAction)->useName(__('app.delete'))
            ->useRepository(static::repository()),

            (new DeleteAction)->isBulk()
            ->useName(__('app.delete'))
            ->useRepository(static::repository())
            ->authorizedToRunWhen(
                fn ($request, $model) => $request->user()->can('bulk delete contacts')
            ),
        ];
    }

    /**
    * Get the displayable label of the resource.
    */
    public static function label() : string
    {
        return __('contact.contacts');
    }

    /**
    * Get the displayable singular label of the resource.
    */
    public static function singularLabel() : string
    {
        return __('contact.contact');
    }

    /**
    * Register permissions for the resource
    */
    public function registerPermissions() : void
    {
        $this->registerCommonPermissions();

        Permissions::register(function ($manager) {
            $manager->group($this->name(), function ($manager) {
                $manager->view('export', [
                    'permissions' => [
                        'export contacts' => __('app.export.export'),
                    ],
                ]);
            });
        });
    }



    /**
     * Get the duplicate finder instance
     */
    public function finder() : RecordFinder
    {
        if ($this->finder) {
            return $this->finder;
        }

        return $this->finder = (new RecordFinder($this->repository()))->with('phones');
    }

    /**
    * Serialize the resource
    */
    public function jsonSerialize() : array
    {
        return parent::jsonSerialize();
    }
}
