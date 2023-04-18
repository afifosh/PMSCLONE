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

namespace App\Innoclapps\Export;

use App\Innoclapps\Fields\Field;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Innoclapps\Contracts\Fields\Dateable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Innoclapps\Export\Exceptions\InvalidExportTypeException;

abstract class ExportViaFields implements WithHeadings, WithMapping, FromCollection
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $cachedFields;

    /**
     * The allowed export file types
     *
     * @var array
     */
    const ALLOWED_TYPES = [
        'csv'  => \Maatwebsite\Excel\Excel::CSV,
        'xls'  => \Maatwebsite\Excel\Excel::XLS,
        'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
    ];

    /**
     * Default export type
     *
     * @var string
     */
    const DEFAULT_TYPE = 'csv';

    /**
     * Provides the exportable fields
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    abstract public function fields();

    /**
     * The export file name (without extension)
     *
     * @return string
     */
    abstract public function fileName() : string;

    /**
     * Perform and download the export
     *
     * @param string|null $type
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $type = null)
    {
        return Excel::download(
            $this,
            $this->fileName() . '.' . $type,
            $this->determineType($type)
        );
    }

    /**
     * Map the export rows
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function map($model) : array
    {
        return $this->resolveFields()->map->resolveForExport($model)->all();
    }

    /**
     * Provides the export eadings
     *
     * @return array
     */
    public function headings() : array
    {
        return $this->resolveFields()->map(fn ($field) => $this->heading($field))->all();
    }

    /**
     * Create heading for export for the given field
     *
     * @param \App\Innoclapps\Fields\Field $field
     *
     * @return string
     */
    public function heading(Field $field) : string
    {
        if ($field instanceof Dateable) {
            return $field->label . ' (' . config('app.timezone') . ')';
        }

        return $field->label;
    }

    /**
     * Get exportable fields
     *
     * @return \Illuminate\Support\Collection
     */
    public function resolveFields()
    {
        if (! $this->cachedFields) {
            $this->cachedFields = $this->filterFieldsForExport($this->fields());
        }

        return $this->cachedFields;
    }

    /**
     * Filter fields for export
     *
     * @param \Illuminate\Support\Collection $fields
     *
     * @return \Illuminate\Support\Collection
     */
    protected function filterFieldsForExport($fields)
    {
        return $fields->reject(fn ($field) => $field->excludeFromExport)->values();
    }

    /**
     * Determine the type
     *
     * @throws \App\Innoclapps\Export\Exceptions\InvalidExportTypeException
     *
     * @param string|string $type
     *
     * @return string
     */
    protected function determineType(?string $type) : string
    {
        if (is_null($type)) {
            return static::ALLOWED_TYPES[static::DEFAULT_TYPE];
        } elseif (! array_key_exists($type, static::ALLOWED_TYPES)) {
            throw new InvalidExportTypeException($type);
        }

        return static::ALLOWED_TYPES[$type];
    }
}
