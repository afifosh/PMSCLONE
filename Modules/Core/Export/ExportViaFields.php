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

namespace Modules\Core\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Contracts\Fields\Dateable;
use Modules\Core\Export\Exceptions\InvalidExportTypeException;
use Modules\Core\Fields\Field;
use Modules\Core\Fields\FieldsCollection;

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
        'csv' => \Maatwebsite\Excel\Excel::CSV,
        'xls' => \Maatwebsite\Excel\Excel::XLS,
        'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
    ];

    /**
     * Default export type
     *
     * @var string
     */
    const DEFAULT_TYPE = 'csv';

    /**
     * Provides the exportable fields.
     */
    abstract public function fields(): FieldsCollection;

    /**
     * The export file name (without extension)
     */
    abstract public function fileName(): string;

    /**
     * Perform and download the export
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $type = null)
    {
        return Excel::download(
            $this,
            $this->fileName().'.'.$type,
            $this->determineType($type)
        );
    }

    /**
     * Map the export rows
     *
     * @param  \Modules\Core\Models\Model  $model
     */
    public function map($model): array
    {
        return $this->resolveFields()->map->resolveForExport($model)->all();
    }

    /**
     * Provides the export eadings.
     */
    public function headings(): array
    {
        return $this->resolveFields()->map(fn ($field) => $this->heading($field))->all();
    }

    /**
     * Create heading for export for the given field.
     */
    public function heading(Field $field): string
    {
        if ($field instanceof Dateable) {
            return $field->label.' ('.config('app.timezone').')';
        }

        return $field->label;
    }

    /**
     * Get exportable fields.
     */
    public function resolveFields(): FieldsCollection
    {
        if (! $this->cachedFields) {
            $this->cachedFields = $this->filterFieldsForExport($this->fields());
        }

        return $this->cachedFields;
    }

    /**
     * Filter fields for export.
     */
    protected function filterFieldsForExport(FieldsCollection $fields): FieldsCollection
    {
        return $fields->reject(fn ($field) => $field->excludeFromExport)->values();
    }

    /**
     * Determine the type.
     *
     * @throws \Modules\Core\Export\Exceptions\InvalidExportTypeException
     */
    protected function determineType(?string $type): string
    {
        if (is_null($type)) {
            return static::ALLOWED_TYPES[static::DEFAULT_TYPE];
        } elseif (! array_key_exists($type, static::ALLOWED_TYPES)) {
            throw new InvalidExportTypeException($type);
        }

        return static::ALLOWED_TYPES[$type];
    }
}
