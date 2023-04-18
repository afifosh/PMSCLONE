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

namespace App\Resources\Contact\Fields;

use App\Resources\Contact\Contact;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Fields\MorphToMany;
use App\Http\Resources\ContactResource;
use App\Contracts\Repositories\ContactRepository;

class Contacts extends MorphToMany
{
    public ?int $order = 1000;

    protected ContactRepository $repository;

    protected static Contact $resource;

    /**
     * Create new instance of Contacts field
     *s
     * @param string $companies
     * @param string $label Custom label
     */
    public function __construct($relation = 'contacts', $label = null)
    {
        parent::__construct($relation, $label ?? __('contact.contacts'));

        $this->repository = app(ContactRepository::class);

        static::$resource = Innoclapps::resourceByName('contacts');

        $this->setJsonResource(ContactResource::class)
            ->labelKey('display_name')
            ->valueKey('id')
            // Used for export
            ->displayUsing(
                fn ($model) => $model->contacts->map(fn ($contact) => $contact->displayName)->implode(', ')
            )
            ->excludeFromImportSample()
            ->excludeFromZapierResponse()
            ->async('/contacts/search')
            ->tapIndexColumn(function ($column) {
                if (! $this->counts()) {
                    $column->useComponent('table-presentable-data-column');
                }
                // For display_name append
                $column->queryAs('first_name')->select(['last_name']);
            })->provideSampleValueUsing(fn () => [1, 2]);
    }

    /**
     * Resolve the field value for import
     *
     * @param string|null $value
     * @param array $row
     * @param array $original
     *
     * @return array|null
     */
    public function resolveForImport($value, $row, $original)
    {
        if (! $value) {
            return $value;
        }

        // Perhaps int e.q. when ID provided?
        $value = is_string($value) ? explode(',', $value) : [$value];

        $ids = collect($value)->map(
            fn ($value) => $this->convertImportValueToId($value)
        )->filter()->values()->all();

        return[$this->attribute => $ids];
    }

    /**
     * Convert import provided value to ID
     *
     * @param int|string|null $value
     *
     * @return mixed
     */
    protected function convertImportValueToId($value)
    {
        if (! $value) {
            return $value;
        }

        // ID provided?
        if (is_numeric($value)) {
            return $value;
        }

        $value = trim($value);

        $contact = static::$resource->finder()->matchByFullName($value) ?: static::$resource->finder()->match([
            'email' => $value,
        ]);

        if ($contact?->trashed()) {
            $this->repository->restore($contact->getKey());
        }

        return $contact?->getKey();
    }
}
