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

use Illuminate\Support\Str;
use App\Innoclapps\Table\BelongsToColumn;
use App\Innoclapps\Repository\BaseRepository;

class BelongsTo extends Optionable
{
    use Selectable;

    /**
     * From where the label key should be taken
     */
    public string $labelKey = 'name';

    /**
     * Can be used to connect multiple fields
     *
     * @var null|\App\Innoclapps\Fields\BelongsTo|\App\Innoclapps\Fields\Field
     */
    public $dependsOn;

    /**
     * The relation name related to $dependsOn
     */
    public ?string $dependsOnRelation = null;

    /**
     * Field component
     */
    public ?string $component = 'belongs-to-field';

    /**
     * Field relationship name
     */
    public string $belongsToRelation;

    /**
     * Field JSON Resource
     */
    protected ?string $jsonResource = null;

    /**
     * The relation repository
     *
     * @var \App\Innoclapps\Repository\BaseRepository
     */
    protected $repository;

    /**
     * Indicates whether new record will be created when the field accepts label as value
     * in case the provided value does not exists in the database
     */
    protected bool $createRecordIfLabelIsMissing = true;

    /**
     * Create new instance of BelongsTo field
     *
     * @param string $name
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     * @param string $label Label
     */
    public function __construct($name, $repository, $label = null, $attribute = null)
    {
        $this->repository = ! $repository instanceof BaseRepository
            ? resolve($repository)
            : $repository;

        $this->belongsToRelation = $name;
        $this->valueKey          = $this->repository->getModel()->getKeyName();

        parent::__construct($attribute ?? $this->repository->getModel()->getForeignKey(), $label);
    }

    /**
     * Set the JSON resource class for the BelongsTo relation
     */
    public function setJsonResource(?string $resourceClass)
    {
        $this->jsonResource = $resourceClass;

        return $this;
    }

    /**
     * Get the relation repository
     *
     * @return \App\Innoclapps\Repository\BaseRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Provide the column used for index
     */
    public function indexColumn() : BelongsToColumn
    {
        return new BelongsToColumn($this->belongsToRelation, $this->labelKey, $this->label);
    }

    /**
     * Connect the fields with another fields
     * e.q. can be used in imports to determine e.q. the proper stage for the pipeline
     */
    public function dependsOn(BelongsTo $field, string $relation) : static
    {
        $this->dependsOn         = $field;
        $this->dependsOnRelation = $relation;

        return $this;
    }

    /**
     * Provides the BelongsTo instance options
     *
     * @return array
     */
    public function resolveOptions()
    {
        $options = parent::resolveOptions();

        if (count($options) === 0 &&
            ! isset($this->meta['asyncUrl'])) {
            $options = $this->repository->columns([$this->labelKey, $this->valueKey])
                ->orderBy($this->labelKey)
                ->all();
        }

        return $options;
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
        if (is_callable($this->displayCallback)) {
            return parent::resolveForDisplay($model);
        }

        return $model->{$this->belongsToRelation}->{$this->labelKey} ?? null;
    }

    /**
    * Get the sample value for the field
    *
    * @return string
    */
    public function sampleValue()
    {
        if ($this->dependsOn) {
            $dependent = $this->dependsOn->getRepository()
                ->first()
                ->{$this->dependsOnRelation}()
                ->first();

            return $dependent->{$this->acceptLabelAsValue ? $this->labelKey : $this->valueKey};
        }

        return parent::sampleValue();
    }

    /**
     * Resolve the value for JSON Resource
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array|null
     */
    public function resolveForJsonResource($model)
    {
        if (! $this->shouldResolveForJson($model)) {
            // Only return the foreign key
            return [$this->attribute => $model->{$this->attribute}];
        }

        return with($this->jsonResource, function ($jsonResource) use ($model) {
            return [
                    $this->belongsToRelation => new $jsonResource($model->{$this->belongsToRelation}),
                    $this->attribute         => $this->resolve($model),
                ];
        });
    }

    /**
     * Get the mailable template placeholder
     *
     * @param \App\Innoclapps\Models\Model|null $model
     *
     * @return \App\Innoclapps\Placeholders\Placeholder
     */
    public function mailableTemplatePlaceholder($model)
    {
        return parent::mailableTemplatePlaceholder($model)
            ->tag(Str::snake(Str::replaceLast('_id', '', $this->belongsToRelation)));
    }

    /**
     * Get the field value when label is provided
     *
     * @param string $value
     * @param array $input
     *
     * @return mixed
     */
    protected function parseValueAsLabelViaOptionable($value, $input)
    {
        if (! $this->dependsOn) {
            return parent::parseValueAsLabelViaOptionable($value, $input);
        }

        $dependsOnId = $input[$this->dependsOn->attribute] ?? null;

        if (! $dependsOnId) {
            throw new \Exception(
                'The ' . $this->dependsOn->attribute . ' must be provided when providing label based ' . $this->attribute . '. Either provide the actual ' . $this->attribute . ' as ID or include the ' . $this->dependsOn->attribute . ' value in as ID'
            );
        }

        return $this->findDependentOptionByLabel($value, $dependsOnId);
    }

    /**
     * Find dependent option by given label
     *
     * @param string $value
     * @param int|string|null $dependsOnId
     *
     * @return mixed
     */
    public function findDependentOptionByLabel($value, $dependsOnId)
    {
        $value = is_string($value) ? Str::lower($value) : $value;

        return $this->getCachedOptionsCollection()->first(function ($option) use ($value, $dependsOnId) {
            // In case the depends on field accept label as value and the $dependsOnId is
            // provided as label, try to find the actual depends on id from the field option
            if (! is_numeric($dependsOnId) && ! is_null($dependsOnId) && $this->dependsOn->acceptLabelAsValue) {
                $dependsOnId = $this->dependsOn->optionByLabel($dependsOnId)[$this->dependsOn->valueKey] ?? null;
            }

            $option = (object) $option;

            $optionLabel = is_string($option->{$this->labelKey}) ?
                Str::lower($option->{$this->labelKey}) :
                $option->{$this->labelKey};

            return $optionLabel == $value && $dependsOnId == $option->{$this->dependsOn->attribute};
        })[$this->dependsOn->valueKey] ?? null;
    }

    /**
     * Accept string value
     *
     * @param bool $createIfMissing
     *
     * @return static
     */
    public function acceptLabelAsValue(bool $createIfMissing = true)
    {
        $this->createRecordIfLabelIsMissing = $createIfMissing;

        return parent::acceptLabelAsValue();
    }

    /**
     * Resolve the field value for import
     *
     * @param string|null $value
     * @param array $row
     * @param array $original
     *
     * @return array
     */
    public function resolveForImport($value, $row, $original)
    {
        $value = parent::resolveForImport(
            $value,
            $row,
            $original
        )[$this->attribute];

        // The labelAsValue was unable to find id for the provided label
        // In this case, we will try to create the actual option in database
        if (is_null($value) && is_string($original[$this->attribute])) {
            // Original provided label
            $label = $original[$this->attribute];

            // First check if the option actually exists in the options collection
            // Perhaps was created in the below create code block
            if ($this->dependsOn &&
                $option = $this->findDependentOptionByLabel($label, $row[$this->dependsOn->attribute] ?? null)) {
                $value = $option[$this->valueKey];
            } elseif (! $this->dependsOn && $option = $this->optionByLabel($label)) {
                $value = $option[$this->valueKey];
            } elseif ($this->createRecordIfLabelIsMissing) {
                $value = $this->repository->create(
                    array_filter(array_merge([
                        $this->labelKey => $label,
                        $this->dependsOn ? [$this->dependsOn->attribute => $row[$this->dependsOn->attribute]] : null,
                    ]))
                )->getKey();

                // Clear the cached options collection as next rows may
                // contain the same option and in this case, because the collection
                // was cached, the newly created option won't be available
                $this->clearCachedOptionsCollection();
            }
        }

        return [$this->attribute => $value];
    }

    /**
     * Check whether the fields values should be resolved for JSON resource
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return boolean
     */
    protected function shouldResolveForJson($model)
    {
        return $model->relationLoaded($this->belongsToRelation) && $this->jsonResource;
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
            'belongsToRelation' => $this->belongsToRelation,
            'dependsOn'         => $this->dependsOn ? $this->dependsOn->attribute : null,
        ]);
    }
}