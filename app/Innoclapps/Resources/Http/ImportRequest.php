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

class ImportRequest extends ResourcefulRequest
{
    /**
     * The row number the request is intended for
     *
     * @var integer|null
     */
    public ?int $rowNumber = null;

    /**
     * @var \App\Innoclapps\Fields\FieldCollection
     */
    protected $fields;

    /**
     * The original import data
     */
    protected array $originalImport = [];

    /**
     * Get fields for the import
     *
     * @return \App\Innoclapps\Fields\FieldCollection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Get the authorized fields for import
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function authorizedFields()
    {
        return $this->fields();
    }

    /**
     * Set the fields for the import request
     *
     * @param \App\Innoclapps\Fields\FieldCollection $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the original row data
     *
     * @return array
     */
    public function original()
    {
        return $this->originalImport;
    }

    /**
     * Set the original row data
     *
     * @param array $row
     */
    public function setOriginal($row)
    {
        $this->originalImport = $row;

        return $this;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
    * Get the error messages for the current resource request
    *
    * @return array
    */
    public function messages()
    {
        return [
            //
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validateResolved()
    {
        //
    }
}