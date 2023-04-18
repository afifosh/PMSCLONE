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

use Illuminate\Support\Facades\Auth;
use App\Innoclapps\Facades\Innoclapps;

class User extends BelongsTo
{
    /**
     * @var string|null
     */
    public ?string $notification = null;

    /**
     * Whether to skip sending the previously specified notification
     *
     * @var boolean
     */
    public static bool $skipNotification = false;

    /**
     * @var string|null
     */
    public ?string $trackChangeDateColumn = null;

    /**
     * The assigneer
     *
     * @var \App\Models\Admin
     */
    public static $assigneer;

    /**
     * Creat new User instance field
     *
     * @param string $label Custom label
     * @param string $relationName
     * @param string|null $attribute
     */
    public function __construct($label = null, $relationName = null, $attribute = null)
    {
        parent::__construct(
            $relationName ?: 'user',
            Innoclapps::getUserRepository(),
            $label ?: __('user.user'),
            $attribute
        );

        // Auth check for console usage
        $this->withDefaultValue(Auth::check() ? $this->createOption(Auth::user()) : null)
            ->importRules($this->getUserImportRules())
            ->tapIndexColumn(fn ($column) => $column->minWidth('100px'));
    }

    /**
     * Skip sending the notification
     *
     * @param boolean $value
     *
     * @return void
     */
    public static function skipNotification(bool $value = true)
    {
        static::$skipNotification = $value;
    }

    /**
     * Provides the User instance options
     *
     * @return \Illuminate\Support\Collection
     */
    public function resolveOptions()
    {
        return $this->repository->columns([$this->valueKey, $this->labelKey, 'avatar'])
            ->orderBy($this->labelKey)
            ->get()
            ->map(fn ($user) => $this->createOption($user));
    }

    /**
     * Set the user that perform the assignee
     *
     * @param \App\Models\Admin $user
     */
    public static function setAssigneer($user)
    {
        static::$assigneer = $user;
    }

    /**
     * Send a notification when the user changes
     *
     * @param string $notification
     *
     * @return static
     */
    public function notification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Set date column to track the date when the user was changed.
     *
     * @param string $dateColumn
     *
     * @return static
     */
    public function trackChangeDate($dateColumn)
    {
        $this->trackChangeDateColumn = $dateColumn;

        return $this;
    }

    /**
     * Handle the resource record "creating" event
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function recordCreating($model)
    {
        $foreignKey = $model->{$this->belongsToRelation}()->getForeignKeyName();

        if ($this->trackChangeDateColumn && ! empty($model->{$foreignKey})) {
            $model->{$this->trackChangeDateColumn} = now();
        }
    }

    /**
     * Handle the resource record "created" event
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function recordCreated($model)
    {
        $this->handleUserChangedNotification($model);
    }

    /**
     * Handle the resource record "updating" event
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function recordUpdating($model)
    {
        if ($this->trackChangeDateColumn) {
            $date       = false;
            $foreignKey = $model->{$this->belongsToRelation}()->getForeignKeyName();

            if (empty($model->{$foreignKey})) {
                $date = null;
            } elseif ($model->getOriginal($foreignKey) !== $model->{$foreignKey}) {
                $date = now();
            }

            if ($date !== false) {
                $model->{$this->trackChangeDateColumn} = $date;
            }
        }
    }

    /**
     * Handle the resource record "updated" event
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function recordUpdated($model)
    {
        $this->handleUserChangedNotification($model);
    }

    /**
     * Handle the user changed notification
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function handleUserChangedNotification($model)
    {
        if (! $this->notification || static::$skipNotification) {
            return;
        }

        $foreignKey = $model->{$this->belongsToRelation}()->getForeignKeyName();

        if (
             // Asssigned user not found
            ! $model->{$foreignKey} ||
            // Is update and the assigned is the same like it was before
            (! $model->wasRecentlyCreated
                && $model->getOriginal($foreignKey) === $model->{$foreignKey}) ||
            // The assigned user is the same as the logged in user
            $model->{$foreignKey} && $model->{$foreignKey} === Auth::id()
        ) {
            return;
        }

        $assigneer = static::$assigneer ?? Auth::user();

        if (! $assigneer) {
            return;
        }

        // We will check if there an assigneer, if not, we won't send the notification
        // as well if the assigneer is the same like the actual user from the field
        if ($model->{$foreignKey} && $assigneer->getKey() !== $model->{$foreignKey}) {

            // Do not trigger additional queries to retrieve the record assignee
            // when importing data, in all cases, there are no notifications sent during import
            if (! Innoclapps::isImportInProgress()) {
                $notification = $this->notification;

                $model->{$this->belongsToRelation}->notify(
                    new $notification($model, $assigneer)
                );
            }
        }
    }

    /**
     * Create option for the front-end
     *
     * @param \App\Models\Admin $user
     *
     * @return array
     */
    protected function createOption($user)
    {
        return [
            $this->valueKey => $user->{$this->valueKey},
            $this->labelKey => $user->{$this->labelKey},
            'avatar_url'    => $user->avatar_url,
        ];
    }

    /**
     * Get the user import rules
     *
     * @return array
     */
    protected function getUserImportRules()
    {
        return [function ($attribute, $value, $fail) {
            if (is_null($value)) {
                return;
            }

            if (! $this->getCachedOptionsCollection()->filter(function ($user) use ($value) {
                return $user[$this->valueKey] == $value || $user[$this->labelKey] == $value;
            })->count() > 0) {
                $fail(__('validation.import.user.invalid', ['attribute' => $this->label]));
            }
        }];
    }
}
