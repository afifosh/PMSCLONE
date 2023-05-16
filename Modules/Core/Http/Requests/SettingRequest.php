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

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Modules\Core\EditorPendingMediaProcessor;
use Modules\Core\Settings\DefaultSettings;

class SettingRequest extends FormRequest
{
    /**
     * The original settings values
     */
    protected ?array $originalValues = null;

    /**
     * Editorable fields
     */
    protected array $editor = ['privacy_policy'];

    /**
     * Save the settings via request.
     */
    public function saveSettings(): void
    {
        $this->processEditorFields();

        $this->collect()
            ->filter($this->filterSettingsForStorage(...))
            ->each(function ($value, $name) {
                is_null($value) ? settings()->forget($name) : settings()->set($name, $value);
            })->whenNotEmpty(function () {
                settings()->save();
            });
    }

    /**
     * Filter the settings that are allowed for storage.
     */
    protected function filterSettingsForStorage(mixed $value, string $name): bool
    {
        $required = DefaultSettings::getRequired();

        if (in_array($name, $required) && empty($value)) {
            return false;
        }

        if (Str::startsWith($name, '_')) {
            return false;
        }

        // Settings filter for storage flag

        return true;
    }

    /**
     * Process the editor fields.
     */
    public function processEditorFields(): void
    {
        $this->collect()->filter(
                fn ($value, $name) => in_array($name, $this->editor)
            )
            ->each(function ($value, $name) {
                (new EditorPendingMediaProcessor)->process(
                    $value,
                    $this->getOriginalValues($name) ?? ''
                );
        });
    }

    /**
     * Get the original settings values.
     */
    public function getOriginalValues(?string $name = null): array|string|null
    {
        $settings = $this->originalValues ?? settings()->all();

        return $name ? ($settings[$name] ?? null) : $settings;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [];
    }
}
