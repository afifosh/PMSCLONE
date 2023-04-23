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

namespace App\Innoclapps\Actions;

use JsonSerializable;
use Illuminate\Support\Str;
use App\Innoclapps\Authorizeable;
use Illuminate\Support\Collection;
use App\Http\Requests\ActionRequest;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Repository\BaseRepository;
use App\Innoclapps\Resources\Http\ResourceRequest;

abstract class Action implements JsonSerializable
{
    use Authorizeable;

    /**
     * Indicates that the action will be hidden on the index view
     *
     * @var boolean
     */
    public bool $hideOnIndex = false;

    /**
     * Indicates that the action will be hidden on the view/update view
     *
     * @var boolean
     */
    public bool $hideOnUpdate = false;

    /**
     * Indicates that the action does not have confirmation dialog
     *
     * @var boolean
     */
    public bool $withoutConfirmation = false;

    /**
     * Determine if the action is executable for the given request.
     *
     * @param \App\Http\Requests\ActionRequest $request
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return boolean
     */
    abstract public function authorizedToRun(ActionRequest $request, $model);

    /**
     * Handle method that all actions must implement
     *
     * @param \Illuminate\Support\Collection $models
     * @param \App\Innoclapps\Actions\ActionFields $fields
     *
     * @return mixed
     */
    public function handle(Collection $models, ActionFields $fields)
    {
        return [];
    }

    /**
     * Get the action fields
     *
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $request
     *
     * @return array
     */
    public function fields(ResourceRequest $request) : array
    {
        return [];
    }

    /**
     * Resolve action fields
     *
     * @param \App\Innoclapps\Resources\Http\ResourceRequest $name
     *
     * @return \Illuminate\Support\Collection
     */
    public function resolveFields(ResourceRequest $request)
    {
        return collect($this->fields($request))->filter->authorizedToSee()->values();
    }

    /**
     * Run action based on the request data
     *
     * @param \App\Http\Requests\ActionRequest $request
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     *
     * @return mixed
     */
    public function run(ActionRequest $request, BaseRepository $repository)
    {
        $ids    = $request->input('ids');
        $fields = $request->resolveFields();

        /**
         * Find all models and exclude any models that are not authorized to be handled in this action
         */
        $models = $this->filterForExecution(
            $this->findModelsForExecution($ids, $repository),
            $request
        );

        /**
         * All models excluded? In this case, the user is probably not authorized to run the action
         */
        if ($models->count() === 0) {
            return static::error(__('user.not_authorized'));
        } elseif ($models->count() > (int) config('app.actions.disable_notifications_when_records_are_more_than')) {
            Innoclapps::disableNotifications();
        }

        $response = $this->handle($models, $fields);

        if (Innoclapps::notificationsDisabled()) {
            Innoclapps::enableNotifications();
        }

        if (! is_null($response)) {
            return $response;
        }

        return static::success(__('actions.run_successfully'));
    }

    /**
     * Toasted success alert
     *
     * @param string $message The response message
     *
     * @return array
     */
    public static function success(string $message) : array
    {
        return ['success' => $message];
    }

    /**
     * Toasted info alert
     *
     * @param string $message The response message
     *
     * @return array
     */
    public static function info(string $message) : array
    {
        return ['info' => $message];
    }

    /**
     * Toasted success alert
     *
     * @param string $message The response message
     *
     * @return array
     */
    public static function error(string $message) : array
    {
        return ['error' => $message];
    }

    /**
     * Throw confetti on success
     *
     * @param string $message The response message
     *
     * @return array
     */
    public static function confetti() : array
    {
        return ['confetti' => true];
    }

    /**
     * Filter models for exeuction
     *
     * @param \Illuminate\Support\Collection $models
     * @param \App\Http\Requests\ActionRequest $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function filterForExecution($models, ActionRequest $request)
    {
        return $models->filter(fn ($model) => $this->authorizedToRun($request, $model));
    }

    /**
     * The action human readable name
     *
     * @return string|null
     */
    public function name() : ?string
    {
        return Str::title(Str::snake(get_called_class(), ' '));
    }

    /**
     * Get the URI key for the card.
     *
     * @return string
     */
    public function uriKey() : string
    {
        return Str::kebab(class_basename(get_called_class()));
    }

    /**
     * Message shown when performing the action
     *
     * @return string
     */
    public function message() : string
    {
        return __('actions.confirmation_message');
    }

    /**
     * Set the action to not have confirmation dialog
     *
     * @return static
     */
    public function withoutConfirmation() : static
    {
        $this->withoutConfirmation = true;

        return $this;
    }

    /**
     * Set the action to be available only on index view
     *
     * @return static
     */
    public function onlyOnIndex() : static
    {
        $this->hideOnUpdate = true;
        $this->hideOnIndex  = false;

        return $this;
    }

    /**
     * Set the action to be available only on update view
     *
     * @return static
     */
    public function onlyOnUpdate() : static
    {
        $this->hideOnUpdate = false;
        $this->hideOnIndex  = true;

        return $this;
    }

    /**
     * Return an open new tab response from the action.
     *
     * @param string $url
     *
     * @return array
     */
    public static function openInNewTab(string $url) : array
    {
        return ['openInNewTab' => $url];
    }

    /**
     * Query the models for execution
     *
     * @param array $ids
     * @param \App\Innoclapps\Repository\AppRepository $repository
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function findModelsForExecution($ids, $repository)
    {
        return $repository->findMany($ids);
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'name'                => $this->name(),
            'message'             => $this->message(),
            'destroyable'         => $this instanceof DestroyableAction,
            'withoutConfirmation' => $this->withoutConfirmation,
            'fields'              => $this->resolveFields(app(ResourceRequest::class)),
            'hideOnIndex'         => $this->hideOnIndex,
            'hideOnUpdate'        => $this->hideOnUpdate,
            'uriKey'              => $this->uriKey(),
        ];
    }
}
