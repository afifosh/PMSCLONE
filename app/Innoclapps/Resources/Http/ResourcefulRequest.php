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

namespace App\Innoclapps\Resources\Http;

use App\Innoclapps\Fields\FieldsCollection;
use App\Innoclapps\Contracts\Resources\Resourceful;

class ResourcefulRequest extends ResourceRequest
{
    use InteractsWithResourceFields;

    /**
     * Get the class of the resource being requested.
     *
     * @return \App\Innoclapps\Resources\Resource
     */
    public function resource()
    {
        return tap(parent::resource(), function ($resource) {
            abort_if(! $resource instanceof Resourceful, 404);
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->authorizedFields()->reject(fn ($field) => empty($field->label))
            ->mapWithKeys(function ($field) {
                return [$field->requestAttribute() => html_entity_decode(strip_tags(trim($field->label)))];
            })->all();
    }

    /**
    * Get the error messages for the current resource request
    *
    * @return array
    */
    public function messages()
    {
        return array_merge($this->authorizedFields()->map(function ($field) {
            return $field->prepareValidationMessages();
        })->filter()->collapse()->all(), $this->messagesFromResource());
    }

    /**
     * Get the error messages that are defined from the resource class
     *
     * @return void
     */
    public function messagesFromResource()
    {
        return $this->resource()->validationMessages();
    }

    /**
     * Get the resource authorized fields for the request
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function authorizedFields()
    {
        if (! $this->isSaving()) {
            return new FieldsCollection;
        }

        return $this->fields()->filter(function ($field) {
            return ! $field->isReadOnly();
        });
    }

    /**
     * Get all the available fields for the request
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function allFields()
    {
        if (! $this->isSaving()) {
            return new FieldsCollection;
        }

        return $this->resource()->setModel(
            $this->resourceId() ? $this->record() : null
        )->getFields();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (! $this->isSaving()) {
            return [];
        }

        return array_merge_recursive(
            $this->resource()->rules($this),
            $this->isCreateRequest() ?
                    $this->resource()->createRules($this) :
                    $this->resource()->updateRules($this),
            $this->authorizedFields()->mapWithKeys(function ($field) {
                return $this->isCreateRequest() ? $field->getCreationRules() : $field->getUpdateRules();
            })->all()
        );
    }

    /**
     * Check whether is saving
     *
     * @return boolean
     */
    public function isSaving()
    {
        return ($this->isMethod('POST') && $this->route()->getActionMethod() === 'store') ||
                ($this->isMethod('PUT') && $this->route()->getActionMethod() === 'update');
    }

    /**
     * Find record for the currently set resource from unique custom fields
     *
     * @return \App\Innoclapps\Models\Model|null
     */
    public function findRecordFromUniqueCustomFields()
    {
        foreach ($this->fields()->filter->isCustomField()->filter(
            fn ($field) => $field->customField->is_unique
        ) as $field) {
            if ($record = $this->resource()->finder()->match([
                $field->attribute => $this->input($field->requestAttribute()),
            ])) {
                return $record;
            }
        }

        return null;
    }
}
