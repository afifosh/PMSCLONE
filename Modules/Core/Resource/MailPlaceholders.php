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

namespace Modules\Core\Resource;

use KubAT\PhpSimple\HtmlDomParser;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Fields\Field;
use Modules\Core\Placeholders\Collection;
use Modules\Core\Placeholders\GenericPlaceholder;
use Modules\Core\Placeholders\Placeholder;
use Modules\Core\Placeholders\UrlPlaceholder;

class MailPlaceholders extends Collection
{
    /**
     * @var \Modules\Core\Resource\Resource
     */
    protected Resource $resource;

    /**
     * Placeholder selector.
     */
    protected static string $placeholderSelector = '._placeholder';

    /**
     * Initialze new  MailPlaceholders instance
     *
     * @param  \Modules\Core\Models\Model|null  $model Provide the model when parsing is needed
     */
    public function __construct(Resource|string $resource, protected $model = null)
    {
        $this->resource = is_string($resource) ?
            Innoclapps::resourceByName($resource) :
            $resource;

        parent::__construct([]);

        $this->setPlaceholders();
    }

    /**
     * Push an URL placeholder to the placeholders array
     *
     * @return static
     */
    public function withUrlPlaceholder()
    {
        $this->push(
            UrlPlaceholder::make($this->model)
                ->description($this->resource->singularLabel().' URL')
        );

        return $this;
    }

    /**
     * Get the resource class the placeholders are intended for
     *
     * @return \Modules\Core\Resource\Resource
     */
    public function getResourceInstance()
    {
        return $this->resource;
    }

    /**
     * Create placeholders groups for edit as fields from the given resourcee names
     *
     *
     * @return \Illuminate\Support\Collection
     */
    public static function createGroupsFromResources(array $resources)
    {
        return collect($resources)->mapWithKeys(function ($resourceName) {
            return with(new static($resourceName), fn ($placeholders) => [$resourceName => [
                'label' => $placeholders->getResourceInstance()->singularLabel(),
                'placeholders' => $placeholders,
            ]]);
        })->reject(fn ($group) => empty($group['placeholders']));
    }

    /**
     * Clean up the given content
     *
     * @param  string  $content
     * @return string
     */
    public static function cleanUpWhenViaInputFields($content)
    {
        if (empty($content)) {
            return $content;
        }

        $dom = HtmlDomParser::str_get_html($content);
        $placeholders = $dom->find(static::$placeholderSelector);

        foreach ($placeholders as $input) {
            $input->outertext = ! empty($input) ? trim($input->value) : '';
        }

        return $dom->save();
    }

    /**
     * Parse the resource placeholders
     *
     * @return array
     */
    public function parseWhenViaInputFields($content)
    {
        if (empty($content)) {
            return $content;
        }

        $placeholders = $this->parse();
        $dom = HtmlDomParser::str_get_html($content);
        $domPlaceholders = $dom->find(static::$placeholderSelector);

        foreach ($domPlaceholders as $input) {
            foreach ($placeholders as $tag => $value) {
                if (empty(trim($input->value)) &&
                            $input->getAttribute('data-tag') == $tag) {
                    $input->value = $value;

                    if (! empty($value)) {
                        $input->setAttribute('data-autofilled', true);
                    }
                }
            }
        }

        return $dom->save();
    }

    /**
     * Set the resource placeholders
     */
    protected function setPlaceholders()
    {
        $this->push(
            $this->resource->resolveFields()
                ->map(function (Field $field) {
                    $placeholder = $field->mailableTemplatePlaceholder($this->model);

                    if ($placeholder instanceof Placeholder) {
                        return $placeholder;
                    } elseif (is_string($placeholder)) { // Allow pass value directly without providing placeholder
                        return GenericPlaceholder::make()
                            ->tag($field->attribute)
                            ->description($field->label)
                            ->value($placeholder);
                    }
                })
                ->filter() // remove empty
                ->all()
        );
    }
}
