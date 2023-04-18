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

use Exception;
use App\Innoclapps\Table\Column;

class IntroductionField extends Field
{
    /**
     * Field component
     */
    public ?string $component = 'introduction-field';

    /**
     * Field title icon
     *
     * @var string|null
     */
    public ?string $titleIcon = null;

    /**
     * Initialize new IntroductionField
     *
     * @param string $title
     * @param string|null $message
     */
    public function __construct(public string $title, public ?string $message = null)
    {
        $this->excludeFromImport();
        $this->excludeFromExport();
        $this->excludeFromSettings();
        $this->excludeFromIndex();
    }

    /**
     * Set field title
     *
     * @param string $title
     *
     * @return static
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set field message
     *
     * @param string|null $message
     *
     * @return static
     */
    public function message(?string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set field title icon
     *
     * @param string|null $icon
     *
     * @return static
     */
    public function titleIcon(?string $icon)
    {
        $this->titleIcon = $icon;

        return $this;
    }

    /**
     * Get the mailable template placeholder
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return null
     */
    public function mailableTemplatePlaceholder($model)
    {
        return null;
    }

    /**
     * Provide the column used for index
     *
     * @return null
     */
    public function indexColumn() : ?Column
    {
        return null;
    }

    /**
    * Resolve the actual field value
    *
    * @param \Illuminate\Database\Eloquent\Model $model
    *
    * @return mixed
    */
    public function resolve($model)
    {
        return null;
    }

    /**
     * Resolve the displayable field value
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return null
     */
    public function resolveForDisplay($model)
    {
        return null;
    }

    /**
     * Resolve the field value for export
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return null
     */
    public function resolveForExport($model)
    {
        return null;
    }

    /**
     * Resolve the field value for import
     *
     * @param string|null $value
     * @param array $row
     * @param array $original
     *
     * @return null
     */
    public function resolveForImport($value, $row, $original)
    {
        return null;
    }

    /**
     * Resolve the field value for JSON Resource
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return null
     */
    public function resolveForJsonResource($model)
    {
        return null;
    }

    /**
     * Add custom value resolver
     *
     * @param callable $resolveCallback
     *
     * @return static
     */
    public function resolveUsing(callable $resolveCallback)
    {
        throw new Exception(__CLASS__ . ' cannot have custom resolve callback');
    }

    /**
     * Add custom display resolver
     *
     * @param callable $displayCallback
     *
     * @return static
     */
    public function displayUsing(callable $displayCallback)
    {
        throw new Exception(__CLASS__ . ' cannot have custom display callback');
    }

    /**
     * Add custom import value resolver
     *
     * @param callable $importCallback
     *
     * @return static
     */
    public function importUsing(callable $importCallback)
    {
        throw new Exception(__CLASS__ . ' cannot be used in imports');
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
            'title'     => $this->title,
            'message'   => $this->message,
            'titleIcon' => $this->titleIcon,
        ]);
    }
}
