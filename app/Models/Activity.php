<?php

namespace App\Models;

use App\Resources\Prunable;
use Illuminate\Support\Str;
use App\Contracts\Calendarable;
use App\Innoclapps\Date\Carbon;
use App\Innoclapps\Models\Model;
use App\Innoclapps\Media\HasMedia;
use App\Support\Concerns\HasComments;
use App\Innoclapps\Concerns\HasCreator;
use App\Innoclapps\Contracts\Presentable;
use App\Innoclapps\Timeline\Timelineable;
use App\Models\Calendar as CalendarModel;
use Illuminate\Database\Query\Expression;
use App\Innoclapps\Resources\Resourceable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\IcalendarGenerator\Components\Calendar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;

class Activity extends Model implements Presentable
{
    use HasFactory,
        HasCreator,
        HasMedia,
        Resourceable,
        Timelineable,
        SoftDeletes;

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
        'activity_type_id'        => 'int',
        'reminder_at'             => 'datetime',
        'completed_at'            => 'datetime',
        'owner_assigned_date'     => 'datetime',
        'reminded_at'             => 'datetime',
        'reminder_minutes_before' => 'int',
        'user_id'                 => 'int',
        'created_by'              => 'int',
    ];

    /**
     * Boot up model and set default data
     *
     * @return void
     */
    protected static function boot()
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
    }

    /**
     * Scope a query to include overdue activities.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $operator <= for overdue > for not overdue
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query, $operator = '<=')
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $condition
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete($query, $condition = 'and')
    {
        return $query->whereNull('completed_at', $condition);
    }

    /**
     * Scope a query to include completed activities.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $condition
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query, $condition = 'and')
    {
        return $query->whereNotNull('completed_at', $condition);
    }

    /**
     * Determine the reminder at date value
     *
     * @param \App\Models\Activity $model
     *
     * @return null|\Carbon\Carbon
     */
    public static function determineReminderAtDate($model)
    {
        if (is_null($model->reminder_minutes_before)) {
            return;
        }

        if (! $model->due_time) {
            return Carbon::asCurrentTimezone($model->due_date . ' 00:00:00', $model->user)
                ->subMinutes($model->reminder_minutes_before)
                ->inAppTimezone();
        }

        return Carbon::parse($model->full_due_date)->subMinutes($model->reminder_minutes_before);
    }

    /**
     * Check whether the activity is synchronized to a calendar
     *
     * @param \App\Models\Calendar $calendar
     *
     * @return boolean
     */
    public function isSynchronizedToCalendar($calendar) : bool
    {
        if (! $calendar) {
            return false;
        }

        return ! is_null($this->latestSynchronization($calendar));
    }

    /**
     * Check whether the current activity type can be synchronized to calendar
     *
     * @return boolean
     */
    public function typeCanBeSynchronizedToCalendar() : bool
    {
        return in_array($this->activity_type_id, $this->user->calendar->activity_types);
    }

    /**
     * Get the activity latest synchronization for the given calendar
     *
     * @param \App\Models\Calendar|null $calendar
     *
     * @return \App\Models\Calendar|null
     */
    public function latestSynchronization(?CalendarModel $calendar = null) : ?CalendarModel
    {
        return $this->synchronizations->where(
            'id', // calendar id from calendars table
            ($calendar ?? $this->user->calendar)->getKey()
        )[0] ?? null;
    }

    /**
     * Get the activity calendar synchronizations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function synchronizations()
    {
        return $this->belongsToMany(\App\Models\Calendar::class, 'activity_calendar_sync')
            ->withPivot(['event_id', 'synchronized_at'])
            ->orderBy('synchronized_at', 'desc');
    }

    /**
     * Activity has type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(\App\Models\ActivityType::class, 'activity_type_id');
    }

    /**
     * Get the activity owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the ICS file file name when downloaded
     *
     * @return string
     */
    public function icsFilename() : string
    {
        return Str::slug($this->type->name) . '-' . Carbon::parse($this->full_due_date)->format('Y-m-d-H:i:s');
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
            ->createdAt(new \DateTime((string) $this->created_at, new \DateTimeZone($this->creator->timezone)))
            ->startsAt(new \DateTime((string) $this->full_due_date, new \DateTimeZone($this->creator->timezone)))
            ->endsAt(new \DateTime((string) $this->full_end_date, new \DateTimeZone($this->creator->timezone)))
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

        return Calendar::create()->event($event)
            ->productIdentifier(config('app.name'));
    }

    /**
     * Indicates whether the activity owner is reminded
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isReminded() : Attribute
    {
        return Attribute::get(fn () => ! is_null($this->reminded_at));
    }

    /**
     *  Indicates whether the activity is completed
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isCompleted() : Attribute
    {
        return Attribute::get(fn () => ! is_null($this->completed_at));
    }

    /**
     * Get all of the contacts that are assigned this activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function contacts()
    {
        return $this->morphedByMany(\App\Models\Contact::class, 'activityable');
    }

    /**
     * Get all of the companies that are assigned this activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function companies()
    {
        return $this->morphedByMany(\App\Models\Company::class, 'activityable');
    }

    /**
     * Get all of the deals that are assigned this activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function deals()
    {
        return $this->morphedByMany(\App\Models\Deal::class, 'activityable');
    }

    /**
     * Get all of the guests for the activity.
     */
    public function guests()
    {
        return $this->belongsToMany(\App\Models\Guest::class, 'activity_guest');
    }

    /**
     * Indicates whether the activity is all day event
     *
     * @return boolean
     */
    public function isAllDay() : bool
    {
        return is_null($this->due_time) && is_null($this->end_time);
    }

    /**
     * Get the calendar event start date
     *
     * @return string
     */
    public function getCalendarStartDate() : string
    {
        $instance = Carbon::inUserTimezone($this->full_due_date);

        return $this->due_time ? $instance->format('Y-m-d\TH:i:s') : $instance->format('Y-m-d');
    }

    /**
     * Get the calendar event end date
     *
     * @return string
     */
    public function getCalendarEndDate() : string
    {
        $instance = Carbon::inUserTimezone($this->full_end_date);

        return $this->end_time ? $instance->format('Y-m-d\TH:i:s') : $instance->format('Y-m-d');
    }

    /**
      * Get the displayable title for the calendar
      *
      * @return string
      */
    public function getCalendarTitle() : string
    {
        return $this->type->name . ' - ' . $this->title;
    }

    /**
    * Get the calendar event background color
    *
    * @return string
    */
    public function getCalendarEventBgColor() : string
    {
        return $this->type->swatch_color;
    }

    /**
     * Get the calendar start date column name for query
     *
     * @return string
     */
    public static function getCalendarStartColumnName() : Expression
    {
        return static::dueDateQueryExpression();
    }

    /**
     * Get the calendar end date column name for query
     *
     * @return string
     */
    public static function getCalendarEndColumnName() : Expression
    {
        return static::dateTimeExpression('end_date', 'end_time');
    }

    /**
     * Get the model display name
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function displayName() : Attribute
    {
        return Attribute::get(fn () => $this->title);
    }

    /**
     * Get the URL path
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function path() : Attribute
    {
        return Attribute::get(fn () => "/activities/{$this->id}");
    }

    /**
     * Get isDue attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isDue() : Attribute
    {
        return Attribute::get(
            fn () => ! $this->isCompleted && $this->full_due_date < date('Y-m-d H:i:s')
        );
    }

    /**
     * Get the full due date in UTC including the time (if has)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function fullDueDate() : Attribute
    {
        return Attribute::get(function () {
            $dueDate = $this->asDateTime($this->due_date);

            return $this->due_time ?
            $dueDate->format('Y-m-d') . ' ' . $this->due_time :
            $dueDate->format('Y-m-d');
        });
    }

    /**
     * Get the full end date in UTC including the time (if has)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function fullEndDate() : Attribute
    {
        return Attribute::get(function () {
            $endDate = $this->asDateTime($this->end_date);

            return $this->end_time ?
                $endDate->format('Y-m-d') . ' ' . $this->end_time :
                $endDate->format('Y-m-d');
        });
    }

    /**
     * Create due date expression for query
     *
     * @param string|null $as
     *
     * @return \Illuminate\Database\Query\Expression|null
     */
    public static function dueDateQueryExpression($as = null)
    {
        return static::dateTimeExpression('due_date', 'due_time', $as);
    }

    /**
     * Create date time expression for querying
     *
     * @param string $dateField
     * @param string $timeField
     * @param string|null $as
     *
     * @return \Illuminate\Database\Query\Expression|null
     */
    public static function dateTimeExpression($dateField, $timeField, $as = null)
    {
        $driver = (new static)->getConnection()->getDriverName();

        switch ($driver) {
            case 'mysql':
            case 'pgsql':
            case 'mariadb':
            return \DB::raw('RTRIM(CONCAT(' . $dateField . ', \' \', COALESCE(' . $timeField . ', \'\')))' . ($as ? ' as ' . $as : ''));

            break;
            case 'sqlite':
            return \DB::raw('RTRIM(' . $dateField . ' || \' \' || COALESCE(' . $timeField . ', \'\'))' . ($as ? ' as ' . $as : ''));

            break;
        }
    }
}
