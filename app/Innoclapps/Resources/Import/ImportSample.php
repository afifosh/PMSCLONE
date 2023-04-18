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

namespace App\Innoclapps\Resources\Import;

use Maatwebsite\Excel\Facades\Excel;
use App\Innoclapps\Resources\Resource;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Innoclapps\Fields\FieldsCollection;
use App\Innoclapps\Contracts\Fields\Dateable;

class ImportSample implements FromArray
{
    use ProvidesImportableFields;

    /**
     * Create new Import instance.
     */
    public function __construct(protected Resource $resource)
    {
    }

    /**
     * Resolve the fields for the sample data
     */
    public function resolveSampleFields() : FieldsCollection
    {
        return $this->resolveFields()->reject(
            fn ($field) => $field->excludeFromImportSample
        );
    }

    /**
     * Download sample
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        return Excel::download($this, 'sample.csv');
    }

    /**
     * Creates the sample data rows
     */
    public function array() : array
    {
        return [
            $this->getHeadings(),
            $this->getRow(),
        ];
    }

    /**
     * Get sample headings by fields
     */
    public function getHeadings() : array
    {
        return $this->resolveSampleFields()->map(function ($field) {
            if ($field instanceof Dateable) {
                return $field->label . ' (' . config('app.timezone') . ')';
            }

            return $field->label;
        })->all();
    }

    /**
     * Prepares import sample row
     */
    public function getRow() : array
    {
        return $this->resolveSampleFields()->reduce(function ($carry, $field) {
            $carry[] = $field->sampleValueForImport();

            return $carry;
        }, []);
    }

    /**
     * Provide the resource fields
     */
    public function fields() : FieldsCollection
    {
        return $this->resource->resolveFields();
    }
}
