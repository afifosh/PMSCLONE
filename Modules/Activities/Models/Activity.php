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

namespace Modules\Activities\Models;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Activities\Contracts\Attendeeable;
use Modules\Activities\Database\Factories\ActivityFactory;
use Modules\Activities\Jobs\CreateCalendarEvent;
use Modules\Activities\Jobs\DeleteCalendarEvent;
use Modules\Activities\Models\Calendar as CalendarModel;
use Modules\Comments\Concerns\HasComments;
use Modules\Contacts\Models\Company;
use Modules\Contacts\Models\Contact;
use Modules\Core\Concerns\HasCreator;
use Modules\Core\Concerns\Prunable;
use Modules\Core\Contracts\DisplaysOnCalendar;
use Modules\Core\Contracts\Presentable;
use Modules\Core\Date\Carbon;
use Modules\Core\Media\HasMedia;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Resourceable;
use Modules\Core\Timeline\Timelineable;
use Modules\Deals\Models\Deal;
use Modules\Users\Models\User;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class Activity extends Model implements DisplaysOnCalendar, Presentable
{
    use HasFactory,
        HasCreator,
        HasMedia,
        HasComments,
        Resourceable,
        Timelineable,
        SoftDeletes,
        Prunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'note',
        'due_date', 'due_time', 'end_time', 'end_date',
        'user_id', 'reminder_minutes_before',
        'activity_type_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'activity_type_id' => 'int',
        'reminder_at' => 'datetime',
        'completed_at' => 'datetime',
        'owner_assigned_date' => 'datetime',
        'reminded_at' => 'datetime',
        'reminder_minutes_before' => 'int',
        'user_id' => 'int',
        'created_by' => 'int',
    ];

    /**
     * The fields for the model that are searchable.
     */
    protected static array $searchableFields = [
        'title' => 'like',
        'note' => 'like',
    ];

    /**
     * Indicates whether the activity should be created on the calendar.
     */
    public bool $calendarable = true;

    /**
     * Boot up model and set default data
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->reminder_at = static::determineReminderAtDate($model);
        });

        static::updating(function ($model) {
            // We will update the date only if the attribute is set on the model
            // because if it's not set, probably was not provided and no need to determine or update the reminder_at
            if (array_key_exists('reminder_minutes_before', $model->getAttributes())) {
                tap($model->reminder_at, function ($originalReminder) use (&$model) {
                    $model->reminder_at = static::determineReminderAtDate($model);

                    // We will check if the reminder_at column has been changed, if yes,
                    // we will reset the reminded_at value to null so new reminder can be sent to the user
                    if (is_null($model->reminder_at) || ($model->isReminded && $originalReminder->ne($model->reminder_at))) {
                        $model->reminded_at = null;
                    }
                });
            }
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->purge(false);
            }

            if ($model->calendarable) {
                $model->deleteFromCalendar();
            }
        });

        static::restored(function ($model) {
            if ($model->calendarable && $model->user->canSyncToCalendar() && $model->typeCanBeSynchronizedToCalendar()) {
                CreateCalendarEvent::dispatch($model->user->calendar, $model);
            }
        });
    }

    /**
     * Determine the reminder at date value
     *
     * @param  \Modules\Activities\Models\Activity  $model
     * @return null|\Carbon\Carbon
     */
    public static function determineReminderAtDate($model)
    {
        if (is_null($model->reminder_minutes_before)) {
            return;
        }

        if (! $model->due_time) {
            return Carbon::asCurrentTimezone($model->due_date.' 00:00:00', $model->user)
                ->subMinutes($model->reminder_minutes_before)
                ->inAppTimezone();
        }

        return Carbon::parse($model->full_due_date)->subMinutes($model->reminder_minutes_before);
    }

    /**
     * Check whether the activity is synchronized to a calendar
     */
    public function isSynchronizedToCalendar(?CalendarModel $calendar): bool
    {
        if (! $calendar) {
            return false;
        }

        return ! is_null($this->latestSynchronization($calendar));
    }

    /**
     * Check whether the current activity type can be synchronized to calendar
     */
    public function typeCanBeSynchronizedToCalendar(): bool
    {
        return in_array($this->activity_type_id, $this->user->calendar->activity_types);
    }

    /**
     * Get the activity latest synchronization for the given calendar
     */
    public function latestSynchronization(?CalendarModel $calendar = null): ?CalendarModel
    {
        return $this->synchronizations->where(
            'id', // calendar id from calendars table
            ($calendar ?? $this->user->calendar)->getKey()
        )[0] ?? null;
    }

    /**
     * Get the activity calendar synchronizations
     */
    public function synchronizations(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Activities\Models\Calendar::class, 'activity_calendar_sync')
            ->withPivot(['event_id', 'synchronized_at'])
            ->orderBy('synchronized_at', 'desc');
    }

    /**
     * Activity has type
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(\Modules\Activities\Models\ActivityType::class, 'activity_type_id');
    }

    /**
     * Get the activity owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ICS file file name when downloaded
     */
    public function icsFilename(): string
    {
        return Str::slug($this->type->name).'-'.Carbon::parse($this->full_due_date)->format('Y-m-d-H:i:s');
    }

    /**
     * Generate ICS instance
     *
     * @return \Spatie\IcalendarGenerator\Components\Calendar
     */
    public function generateICSInstance()
    {
        $event = Event::create()
            ->name($this->title)
            ->description($this->description ?? '')
            ->createdAt($this->created_at)
            ->startsAt(new \DateTime($this->full_due_date))
            ->endsAt(new \DateTime($this->full_end_date))
            ->withoutTimezone()
            ->organizer($this->creator->email, $this->creator->name);

        if ($this->isAllDay()) {
            $event->fullDay();
        }

        $this->load('guests.guestable');

        $this->guests->reject(function ($model) {
            return ! $model->guestable->getGuestEmail();
        })->each(function ($model) use ($event) {
            $event->attendee(
                $model->guestable->getGuestEmail(),
                $model->guestable->getGuestDisplayName(),
                ParticipationStatus::accepted() // not working, still show in thunderbird statuses to accept
            );
        });

        return Calendar::create()
            ->withoutAutoTimezoneComponents()
            ->event($event)
            ->productIdentifier(config('app.name'));
    }

    /**
     * Indicates whether the activity owner is reminded
     */
    public function isReminded(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->reminded_at));
    }

    /**
     *  Indicates whether the activity is completed
     */
    public function isCompleted(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->completed_at));
    }

    /**
     * Get all of the contacts that are assigned this activity.
     */
    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(\Modules\Contacts\Models\Contact::class, 'activityable');
    }

    /**
     * Get all of the companies that are assigned this activity.
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(\Modules\Contacts\Models\Company::class, 'activityable');
    }

    /**
     * Get all of the deals that are assigned this activity.
     */
    public function deals(): MorphToMany
    {
        return $this->morphedByMany(\Modules\Deals\Models\Deal::class, 'activityable');
    }

    /**
     * Get all of the guests for the activity.
     */
    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Activities\Models\Guest::class, 'activity_guest');
    }

    /**
     * Indicates whether the activity is all day event
     */
    public function isAllDay(): bool
    {
        return is_null($this->due_time) && is_null($this->end_time);
    }

    /**
     * Get the calendar event start date
     */
    public function getCalendarStartDate(): string
    {
        $instance = Carbon::inUserTimezone($this->full_due_date);

        return $this->due_time ? $instance->format('Y-m-d\TH:i:s') : $instance->format('Y-m-d');
    }

    /**
     * Get the calendar event end date
     */
    public function getCalendarEndDate(): string
    {
        $instance = Carbon::inUserTimezone($this->full_end_date);

        return $this->end_time ? $instance->format('Y-m-d\TH:i:s') : $instance->format('Y-m-d');
    }

    /**
     * Get the displayable title for the calendar
     */
    public function getCalendarTitle(): string
    {
        return $this->type->name.' - '.$this->title;
    }

    /**
     * Get the calendar event background color
     */
    public function getCalendarEventBgColor(): string
    {
        return $this->type->swatch_color;
    }

    /**
     * Get the calendar start date column name for query
     */
    public static function getCalendarStartColumnName(): Expression
    {
        return static::dueDateQueryExpression();
    }

    /**
     * Get the calendar end date column name for query
     */
    public static function getCalendarEndColumnName(): Expression
    {
        return static::dateTimeExpression('end_date', 'end_time');
    }

    /**
     * Get the model display name
     */
    public function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->title);
    }

    /**
     * Get the URL path
     */
    public function path(): Attribute
    {
        return Attribute::get(fn () => "/activities/{$this->id}");
    }

    /**
     * Get isDue attribute
     */
    public function isDue(): Attribute
    {
        return Attribute::get(
            fn () => ! $this->isCompleted && $this->full_due_date < date('Y-m-d H:i:s')
        );
    }

    /**
     * Get the full due date in UTC including the time (if has)
     */
    public function fullDueDate(): Attribute
    {
        return Attribute::get(function () {
            $dueDate = $this->asDateTime($this->due_date);

            return $this->due_time ?
            $dueDate->format('Y-m-d').' '.$this->due_time :
            $dueDate->format('Y-m-d');
        });
    }

    /**
     * Get the full end date in UTC including the time (if has)
     */
    public function fullEndDate(): Attribute
    {
        return Attribute::get(function () {
            $endDate = $this->asDateTime($this->end_date);

            return $this->end_time ?
                $endDate->format('Y-m-d').' '.$this->end_time :
                $endDate->format('Y-m-d');
        });
    }

    /**
     * Create due date expression for query
     */
    public static function dueDateQueryExpression(?string $as = null): ?Expression
    {
        return static::dateTimeExpression('due_date', 'due_time', $as);
    }

    /**
     * Create date time expression for querying
     */
    public static function dateTimeExpression(string $dateField, string $timeField, ?string $as = null): ?Expression
    {
        $driver = (new static)->getConnection()->getDriverName();

        switch ($driver) {
            case 'mysql':
            case 'pgsql':
            case 'mariadb':
                return DB::raw('RTRIM(CONCAT('.$dateField.', \' \', COALESCE('.$timeField.', \'\')))'.($as ? ' as '.$as : ''));

                break;
            case 'sqlite':
                return DB::raw('RTRIM('.$dateField.' || \' \' || COALESCE('.$timeField.', \'\'))'.($as ? ' as '.$as : ''));

                break;
        }
    }

    /**
     * Get the timeline component for front-end
     */
    public function getTimelineComponent(): string
    {
        return 'record-tab-timeline-activity';
    }

    /**
     * Check whether the given guest attends to the activity.
     */
    public function hasGuest(Model&Attendeeable $attendee): bool
    {
        return $this->guests->contains(function ($guest) use ($attendee) {
            return (int) $guest->guestable_id === (int) $attendee->getKey() && $attendee::class === $guest->guestable_type;
        });
    }

    /**
     * Scope a query to include incomplete and in future activities.
     */
    public function scopeIncompleteAndInFuture(Builder $query): Builder
    {
        return $query->incomplete()->where(static::dueDateQueryExpression(), '>=', Carbon::asAppTimezone());
    }

    /**
     * Scope a query to include overdue activities.
     *
     * @param  string  $operator <= for overdue > for not overdue
     */
    public function scopeOverdue(Builder $query, $operator = '<='): Builder
    {
        return $query->incomplete()->where(
            static::dueDateQueryExpression(),
            $operator,
            Carbon::asAppTimezone()
        );
    }

    /**
     * Scope a query to include incompleted activities.
     *
     * @param  string  $condition
     */
    public function scopeIncomplete(Builder $query, $condition = 'and'): Builder
    {
        return $query->whereNull('completed_at', $condition);
    }

    /**
     * Scope a query to include completed activities.
     *
     * @param  string  $condition
     */
    public function scopeCompleted(Builder $query, $condition = 'and'): Builder
    {
        return $query->whereNotNull('completed_at', $condition);
    }

    /**
     * Scope a query to only include activities that are due today.
     */
    public function scopeDueToday(Builder $query): void
    {
        $now = Carbon::asCurrentTimezone();

        $query->whereBetween(
            static::dueDateQueryExpression(),
            [$now->copy()->startOfDay(), $now->endOfDay()]
        )->incomplete();
    }

    /**
     * Scope a query to only include upcoming activities.
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where(static::dueDateQueryExpression(), '>', Carbon::asAppTimezone());
    }

    /**
     * Scope a query to only include activities due for reminder.
     */
    public function scopeReminderable(Builder $query): void
    {
        $query->notReminded()
            ->whereHas('user')
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', Carbon::asAppTimezone());
    }

    /**
     * Scope a query to only include activities that no reminder is sent.
     */
    public function scopeNotReminded(Builder $query): void
    {
        $query->whereNull('reminded_at');
    }

    /**
     * Mark the activity as complete.
     */
    public function markAsComplete(): static
    {
        $this->completed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Mark the activity as incomplete.
     */
    public function markAsIncomplete(): static
    {
        $this->completed_at = null;
        $this->save();

        return $this;
    }

    /**
     * Tap the calendar request query
     */
    public function tapCalendarQuery(Builder $query, Request $request): void
    {
        $user = $request->user();

        // The calendar endpoint by default shows the current user activities
        // however, if the logged-in user is able to view all activities,
        // a user_id param can be provided, when integer queries activities for the given user
        // but if user_id is provided as false e.q. false|off|0, queries all users activities
        if ($user->can('view all activities')) {
            $request->whenHas('user_id', function ($userId) use (&$user) {
                if (! empty($userId = intval($userId))) {
                    $user = User::find($userId);
                } elseif (filter_var($userId, FILTER_VALIDATE_BOOLEAN) === false) {
                    $user = null;
                }
            });
        }

        $query->incomplete()->when(! is_null($user), function ($query) use ($user) {
            return $query->where('user_id', $user->getKey());
        });

        if ($request->filled('activity_type_id')) {
            $query->where('activity_type_id', $request->integer('activity_type_id'));
        }

        $query->with('type');
    }

    /**
     * Tap the calendar request date query
     */
    public function tapCalendarDateQuery(Builder $query, string|Expression $startColumn, string|Expression $endColumn, Request $request): void
    {
        $query->orWhere(function ($query) use ($request) {
            $startInUserTimezone = Carbon::parse($request->start_date)->tz($request->user()->timezone);
            $endInUserTimezone = Carbon::parse($request->end_date)->tz($request->user()->timezone);

            $startFormatted = $startInUserTimezone->format('Y-m-d');
            $endFormatted = $endInUserTimezone->format('Y-m-d');

            // https://stackoverflow.com/questions/17014066/mysql-query-to-select-events-between-start-end-date
            $spanRaw = '? BETWEEN due_date AND end_date';

            return $query->whereRaw(
                "CASE
                    WHEN due_time IS NULL AND end_time IS NULL THEN due_date BETWEEN
                    ? AND ? OR {$spanRaw}
                    WHEN due_time IS NOT NULL AND end_time IS NULL THEN due_date
                    BETWEEN ? AND ? OR {$spanRaw}
                END",
                [$startFormatted, $endFormatted, $startFormatted, $request->start_date, $endFormatted, $startFormatted]
            );
        });
    }

    /**
     * Add new guest(s) to the activity.
     */
    public function addGuest(Attendeeable|array $guests, bool $notify = true): void
    {
        if ($guests instanceof Attendeeable) {
            $guests = [$guests];
        }

        // Todo, in future check if the actual guest exists
        foreach ($guests as $model) {
            $guest = $model->guests()->create([]);
            $guest->activities()->attach($this);

            if ($notify && $model->shouldSendAttendingNotification($model)) {
                $this->sendNotificationToAttendee($model);
            }
        }
    }

    /**
     * Send notification to the given attendee
     */
    protected function sendNotificationToAttendee(Attendeeable $guest): void
    {
        $notification = $guest->getAttendeeNotificationClass();

        if (method_exists($guest, 'notify') && is_a($notification, Notification::class, true)) {
            $guest->notify(new $notification($guest, $this));
        } elseif (is_a($notification, Mailable::class, true) && ! empty($email = $guest->getGuestEmail())) {
            Mail::to($email)->send(new $notification($guest, $this));
        }
    }

    /**
     * Add activity synchronization data
     */
    public function addSynchronization(string|int $eventId, int $calendarId, array $attributes): void
    {
        $this->synchronizations()->attach($calendarId, [
            'synchronized_at' => now(),
            'event_id' => $eventId,
        ] + $attributes);
    }

    /**
     * Update activity synchronization data
     */
    public function updateSynchronization(string|int $eventId, int $calendarId, array $attributes): void
    {
        $this->synchronizations()->where('event_id', $eventId)
            ->where('activity_calendar_sync.calendar_id', $calendarId)
            ->update($attributes);
    }

    /**
     * Delete the activity synchronization data
     */
    public function deleteSynchronization(string|int $eventId, int $calendarId): void
    {
        $this->synchronizations()->where('event_id', $eventId)->detach($calendarId);
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeByEventSyncId(Builder $query, string|int $eventId): void
    {
        $query->withTrashed()->whereHas('synchronizations', fn ($query) => $query->where('event_id', $eventId));
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithResponseRelations(Builder $query): void
    {
        $query->withCount(['comments'])->with([
            'companies.nextActivity',
            'contacts.nextActivity',
            'deals.nextActivity',
            'media',
            'creator',
            'guests.guestable',
        ]);
    }

    /**
     * Delete the activity from the synced calendar.
     */
    public function deleteFromCalendar(?User $user = null)
    {
        $user = $user ?: $this->user;

        if (! $this->isSynchronizedToCalendar($user->calendar)) {
            return;
        }

        $eventId = $this->latestSynchronization($user->calendar)->pivot->event_id;
        $activityId = $this->getKey();

        DeleteCalendarEvent::dispatch($user->calendar, $activityId, $eventId);
    }

    /**
     * Purge the activity data
     */
    public function purge(bool $fromCalendar = true): void
    {
        if ($fromCalendar) {
            $this->deleteFromCalendar();
        }

        foreach (['contacts', 'companies', 'deals'] as $relation) {
            $this->{$relation}()->withTrashed()->detach();
        }

        $this->guests->each(function ($guest) {
            $guest->activities()->withTrashed()->detach();
            $guest->delete();
        });

        foreach ([Contact::class, Company::class, Deal::class] as $model) {
            $model::withTrashed()
                ->where('next_activity_id', $this->getKey())
                ->update(['next_activity_id' => null]);
        }
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }
}
