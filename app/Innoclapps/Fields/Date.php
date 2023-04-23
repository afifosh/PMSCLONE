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

use App\Innoclapps\Facades\Format;
use App\Innoclapps\Table\DateColumn;
use App\Innoclapps\Contracts\Fields\Dateable;
use App\Innoclapps\Placeholders\DatePlaceholder;
use App\Innoclapps\Contracts\Fields\Customfieldable;
use App\Innoclapps\Fields\Dateable as DateableTrait;

class Date extends Field implements Customfieldable, Dateable
{
    use DateableTrait;

    /**
     * Field component
     */
    public ?string $component = 'date-field';

    /**
     * Boot the field
     *
     * @return void
     */
    public function boot()
    {
        $this->rules(['nullable', 'date'])
            ->provideSampleValueUsing(fn () => date('Y-m-d'));
    }

    /**
     * Create the custom field value column in database
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $fieldId
     *
     * @return void
     */
    public static function createValueColumn($table, $fieldId)
    {
        $table->date($fieldId)->nullable();
    }

    /**
     * Resolve the displayable field value
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string|null
     */
    public function resolveForDisplay($model)
    {
        return Format::date($model->{$this->attribute});
    }

    /**
     * Get the mailable template placeholder
     *
     * @param \App\Innoclapps\Models\Model|null $model
     *
     * @return \App\Innoclapps\Placeholders\DatePlaceholder
     */
    public function mailableTemplatePlaceholder($model)
    {
        return DatePlaceholder::make()
            ->value(fn () => $this->resolve($model))
            ->forUser($model?->user)
            ->tag($this->attribute)
            ->description($this->label);
    }

    /**
     * Provide the column used for index
     */
    public function indexColumn() : DateColumn
    {
        return new DateColumn($this->attribute, $this->label);
    }
}
