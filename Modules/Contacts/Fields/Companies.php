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

namespace Modules\Contacts\Fields;

use Modules\Contacts\Http\Resources\CompanyResource;
use Modules\Contacts\Resource\Company\Company;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Fields\MorphToMany;

class Companies extends MorphToMany
{
    public ?int $order = 1001;

    protected static Company $resource;

    /**
     * Create new instance of Companies field
     *s
     *
     * @param  string  $companies
     * @param  string  $label Custom label
     */
    public function __construct($relation = 'companies', $label = null)
    {
        parent::__construct($relation, $label ?? __('contacts::company.companies'));

        static::$resource = Innoclapps::resourceByName('companies');

        $this->setJsonResource(CompanyResource::class)
            ->labelKey('name')
            ->valueKey('id')
            // Used for export
            ->displayUsing(
                fn ($model) => $model->companies->map(fn ($company) => $company->displayName)->implode(', ')
            )
            ->excludeFromZapierResponse()
            ->tapIndexColumn(function ($column) {
                if (! $this->counts()) {
                    $column->useComponent('table-data-presentable-column');
                }
            })
            ->async('/companies/search')
            ->lazyLoad('/companies', ['order' => 'created_at|desc'])
            ->provideSampleValueUsing(fn () => 'Company Name, Other Company Name');
    }

    /**
     * Resolve the field value for import
     *
     * @param  string|null  $value
     * @param  array  $row
     * @param  array  $original
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
            fn ($name) => $this->convertImportValueToId($name)
        )->filter()->values()->all();

        return [$this->attribute => $ids];
    }

    /**
     * Convert import provided value to ID
     *
     * @param  int|string|null  $value
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

        $company = static::$resource->finder()->match(['name' => $value]);

        if ($company?->trashed()) {
            $company->restore();
        }

        return $company?->getKey();
    }
}
