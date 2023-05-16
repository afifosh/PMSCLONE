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

namespace Modules\Core\Resource\Import;

use Modules\Core\Fields\FieldsCollection;

trait ProvidesImportableFields
{
    protected ?FieldsCollection $fields = null;

    /**
     * Provide the resource fields
     */
    abstract public function fields(): FieldsCollection;

    /**
     * Resolve the importable fields
     */
    public function resolveFields(): FieldsCollection
    {
        if (! $this->fields) {
            $this->fields = $this->filterFieldsForImport($this->fields());
        }

        return $this->fields;
    }

    /**
     * Filter fields for import
     */
    protected function filterFieldsForImport(FieldsCollection $fields): FieldsCollection
    {
        return $fields->reject(function ($field) {
            return $field->excludeFromImport || $field->isReadOnly();
        })->values();
    }
}
