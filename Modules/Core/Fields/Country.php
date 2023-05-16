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

namespace Modules\Core\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Http\Resources\CountryResource;
use Modules\Core\Models\Country as CountryModel;

class Country extends BelongsTo
{
    /**
     * Create new instance of Country field
     *
     * @param  string  $label Custom label
     */
    public function __construct($label = null)
    {
        parent::__construct('country', CountryModel::class, $label ?? __('core::country.country'));

        $this->acceptLabelAsValue(false)->setJsonResource(CountryResource::class);
    }

    /**
     * Get the field value when label is provided
     *
     * @param  string  $value
     * @param  array  $input
     * @return int|null
     */
    protected function parseValueAsLabelViaOptionable($value, $input)
    {
        $options = $this->getCachedOptionsCollection();

        // Case sensitive comparision
        return $options->first(function ($country) use ($value) {
            return Str::is($country->name, $value) ||
                Str::is($country->iso_3166_2, $value) ||
                Str::is($country->iso_3166_3, $value) ||
                Str::contains($country->full_name, $value);
        })[$this->valueKey] ?? null;
    }

    /**
     * Get cached options collection
     *
     * When importing data, the label as value function will be called
     * multiple times, we don't want all the queries executed multiple times
     * from the fields which are providing options via model.
     */
    public function getCachedOptionsCollection(): Collection
    {
        if (! $this->cachedOptions) {
            $this->cachedOptions = CountryModel::get();
        }

        return $this->cachedOptions;
    }

    /**
     * Resolve the field value for import
     *
     * @param  string|null  $value
     * @param  array  $row
     * @param  array  $original
     * @return array
     */
    public function resolveForImport($value, $row, $original)
    {
        // If not found via label option, will be null as
        // country cannot be created during import
        return [$this->attribute => $value];
    }
}
