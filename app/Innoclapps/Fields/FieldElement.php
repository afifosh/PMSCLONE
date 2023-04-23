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

namespace App\Innoclapps\Fields;

use App\Innoclapps\Authorizeable;
use App\Innoclapps\MetableElement;

class FieldElement
{
    use Authorizeable,
        MetableElement;

    /**
     * @var boolean
     */
    public bool $showOnIndex = true;

    /**
     * @var boolean|callable
     */
    public $applicableForIndex = true;

    /**
     * @var boolean
     */
    public bool $showOnCreation = true;

    /**
     * @var boolean|callable
     */
    public $applicableForCreation = true;

    /**
     * @var boolean
     */
    public bool $showOnUpdate = true;

    /**
     * @var boolean|callable
     */
    public $applicableForUpdate = true;

    /**
     * @var boolean
     */
    public bool $showOnDetail = true;

    /**
     * @var boolean|callable
     */
    public $applicableForDetail = true;

    /**
     * @var boolean|string
     */
    public bool|string $excludeFromSettings = false;

    /**
     * Indicates whether to exclude the field from import
     *
     * @var boolean
     */
    public bool $excludeFromImport = false;

    /**
     * Indicates whether the field should be included in sample data
     *
     * @var boolean
     */
    public bool $excludeFromImportSample = false;

    /**
     * Indicates whether to exclude the field from export
     *
     * @var boolean
     */
    public bool $excludeFromExport = false;

    /**
     * Set that the field by default should be hidden on index view
     *
     * @return static
     */
    public function hideFromIndex() : static
    {
        $this->showOnIndex = false;

        return $this;
    }

    /**
     * The field is only for index and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForIndex() : static
    {
        $this->excludeFromSettings   = true;
        $this->applicableForIndex    = true;
        $this->applicableForCreation = false;
        $this->applicableForUpdate   = false;
        $this->applicableForDetail   = false;

        return $this;
    }

    /**
     * Set that the field by default should be hidden on create view
     *
     * @return static
     */
    public function hideWhenCreating() : static
    {
        $this->showOnCreation = false;

        return $this;
    }

    /**
     * The field is only for creation and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForCreation() : static
    {
        $this->applicableForCreation = true;
        $this->applicableForUpdate   = false;
        $this->applicableForDetail   = false;
        $this->applicableForIndex    = false;

        return $this;
    }

    /**
     * The field is only for creation and index and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForCreationAndIndex() : static
    {
        $this->applicableForCreation = true;
        $this->applicableForIndex    = true;
        $this->applicableForUpdate   = false;
        $this->applicableForDetail   = false;

        return $this;
    }

    /**
     * Set that the field by default should be hidden on update view
     *
     * @return static
     */
    public function hideWhenUpdating() : static
    {
        $this->showOnUpdate = false;

        return $this;
    }

    /**
     * The field is only for update and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForUpdate() : static
    {
        $this->applicableForUpdate   = true;
        $this->applicableForCreation = false;
        $this->applicableForIndex    = false;
        $this->applicableForDetail   = false;

        return $this;
    }

    /**
     * Set that the field by default should be hidden on detail view
     *
     * @return static
     */
    public function hideFromDetail() : static
    {
        $this->showOnDetail = false;

        return $this;
    }

    /**
     * The field is only for detail and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForDetail() : static
    {
        $this->applicableForDetail   = true;
        $this->applicableForUpdate   = false;
        $this->applicableForCreation = false;
        $this->applicableForIndex    = false;
        $this->applicableForDetail   = false;

        return $this;
    }

    /**
     * The field is only for import and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForImport() : static
    {
        $this->applicableForUpdate   = false;
        $this->applicableForDetail   = false;
        $this->applicableForCreation = false;
        $this->applicableForIndex    = false;
        $this->excludeFromExport     = true;
        $this->excludeFromSettings   = true;
        $this->excludeFromImport     = false;

        return $this;
    }

    /**
     * The field is only for forms and cannot be used on other views
     *
     * @return static
     */
    public function strictlyForForms() : static
    {
        $this->applicableForUpdate   = true;
        $this->applicableForDetail   = true;
        $this->applicableForCreation = true;
        $this->applicableForIndex    = false;

        return $this;
    }

    /**
     * The field is only usable on views different then forms
     *
     * @param boolean|callable $applicable
     *
     * @return static
     */
    public function exceptOnForms(bool|callable $applicable = false) : static
    {
        $this->applicableForUpdate   = $applicable;
        $this->applicableForDetail   = $applicable;
        $this->applicableForCreation = $applicable;
        $this->applicableForIndex    = true;
        $this->excludeFromImport     = true;
        $this->excludeFromSettings   = true;

        return $this;
    }

    /**
     * Set that the field is excluded from index view
     *
     * @param boolean|callable $applicable
     *
     * @return static
     */
    public function excludeFromIndex(bool|callable $applicable = false) : static
    {
        $this->applicableForIndex = $applicable;

        return $this;
    }

    /**
     * Set if the field is excluded from create view
     *
     * @param boolean|callable $applicable
     *
     * @return static
     */
    public function excludeFromCreate(bool|callable $applicable = false) : static
    {
        $this->applicableForCreation = $applicable;

        return $this;
    }

    /**
     * Set if the field is excluded from update view
     *
     * @param boolean|callable $applicable
     *
     * @return static
     */
    public function excludeFromUpdate(bool|callable $applicable = false) : static
    {
        $this->applicableForUpdate = $applicable;

        return $this;
    }

    /**
     * Set if the field is excluded from detail view
     *
     * @param boolean|callable $applicable
     *
     * @return static
     */
    public function excludeFromDetail(bool|callable $applicable = false) : static
    {
        $this->applicableForDetail = $applicable;

        return $this;
    }

    /**
     * Indicates that this field should be excluded from the settings
     *
     * @param string|boolean $view
     *
     * @return static
     */
    public function excludeFromSettings(string|bool $view = true) : static
    {
        $this->excludeFromSettings = $view;

        return $this;
    }

    /**
     * Set that the field should be excluded from export
     *
     * @return static
     */
    public function excludeFromExport() : static
    {
        $this->excludeFromExport = true;

        return $this;
    }

    /**
     * Set that the field should be excluded from import
     *
     * @return static
     */
    public function excludeFromImport() : static
    {
        $this->excludeFromImport = true;

        return $this;
    }

    /**
     * Set that the field should should not be included in sample data
     *
     * @return static
     */
    public function excludeFromImportSample() : static
    {
        $this->excludeFromImportSample = true;

        return $this;
    }

    /**
     * Indicates that the field by default should be hidden on all views.
     *
     * @return static
     */
    public function hidden() : static
    {
        $this->hideWhenUpdating();
        $this->hideWhenCreating();
        $this->hideFromIndex();
        $this->hideFromDetail();

        return $this;
    }

    /**
     * Determine if the field is applicable for creation view
     *
     * @return boolean
     */
    public function isApplicableForCreation() : bool
    {
        return with($this->applicableForCreation, function ($callback) {
            return $callback === true || (is_callable($callback) && call_user_func($callback));
        });
    }

    /**
     * Determine if the field is applicable for update view
     *
     * @return boolean
     */
    public function isApplicableForUpdate() : bool
    {
        return with($this->applicableForUpdate, function ($callback) {
            return $callback === true || (is_callable($callback) && call_user_func($callback));
        });
    }

    /**
     * Determine if the field is applicable for detail view
     *
     * @return bool
     */
    public function isApplicableForDetail() : bool
    {
        return with($this->applicableForDetail, function ($callback) {
            return $callback === true || (is_callable($callback) && call_user_func($callback));
        });
    }

    /**
     * Determine if the field is applicable for index view
     *
     * @return boolean
     */
    public function isApplicableForIndex() : bool
    {
        return with($this->applicableForIndex, function ($callback) {
            return $callback === true || (is_callable($callback) && call_user_func($callback));
        });
    }
}
