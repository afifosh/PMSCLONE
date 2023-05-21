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

namespace Modules\Contacts\Models;

use Modules\Core\Changelog\LogsModelChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Activities\Concerns\HasActivities;
// use Modules\Contacts\Concerns\HasPhones;
use Modules\Contacts\Concerns\HasSource;
use Modules\Contacts\Database\Factories\CompanyFactory;
use Modules\Core\Concerns\HasCountry;
use Modules\Core\Concerns\HasCreator;
use Modules\Core\Concerns\HasUuid;
use Modules\Core\Concerns\Prunable;
use Modules\Core\Contracts\Fields\HandlesChangedMorphManyAttributes;
use Modules\Core\Contracts\Presentable;
use Modules\Core\Media\HasMedia;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Resourceable;
use Modules\Core\Timeline\HasTimeline;
use Modules\Core\Workflow\HasWorkflowTriggers;
// use Modules\Deals\Concerns\HasDeals;
// use Modules\Documents\Concerns\HasDocuments;
use Modules\MailClient\Concerns\HasEmails;

class Company extends Model implements Presentable//, HandlesChangedMorphManyAttributes
{
    use HasCountry,
        HasCreator,
        HasSource,
        LogsModelChanges,
        Resourceable,
        HasUuid,
        HasMedia,
        HasWorkflowTriggers,
        HasTimeline,
        HasEmails,
        // HasDeals,
        // HasDocuments,
        HasActivities,
        HasFactory,
        // HasPhones,
        SoftDeletes,
        Prunable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_by',
        'created_at',
        'updated_at',
        'owner_assigned_date',
        'next_activity_id',
        'uuid',
    ];

    /**
     * Attributes and relations to log changelog for the model
     *
     * @var array
     */
    protected static $changelogAttributes = [
        '*',
        'country.name',
        'parent.name',
        'source.name',
        'user.name',
        'industry.name',
    ];

    /**
     * Exclude attributes from the changelog
     *
     * @var array
     */
    protected static $changelogAttributeToIgnore = [
        'updated_at',
        'created_at',
        'created_by',
        'owner_assigned_date',
        'next_activity_id',
        'deleted_at',
    ];

    /**
     * Provides the relationships for the pivot logger
     *
     * [ 'main' => 'reverse']
     *
     * @return array
     */
    protected static $logPivotEventsOn = [
        'contacts' => 'companies',
        'deals' => 'companies',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'owner_assigned_date' => 'datetime',
        'user_id' => 'int',
        'created_by' => 'int',
        'source_id' => 'int',
        'industry_id' => 'int',
        'parent_company_id' => 'int',
        'country_id' => 'int',
        'next_activity_id' => 'int',
    ];

    /**
     * The fields for the model that are searchable.
     */
    protected static array $searchableFields = [
        'name' => 'like',
        'email' => 'like',
        'domain',
        'phones.number',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::restoring(function ($model) {
            $model->logToAssociatedRelationsThatRelatedInstanceIsRestored(['contacts', 'deals']);
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->purge();
            } else {
                $model->logRelatedIsTrashed(['contacts', 'deals'], [
                    'key' => 'core::timeline.associate_trashed',
                    'attrs' => ['displayName' => $model->display_name],
                ]);
            }
        });
    }

    /**
     * Get the parent company
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(\Modules\Contacts\Models\Company::class, 'parent_company_id');
    }

    /**
     * Get all of the company parent companies
     */
    public function parents(): HasMany
    {
        return $this->hasMany(\Modules\Contacts\Models\Company::class, 'parent_company_id');
    }

    /**
     * Get all of the contacts that are associated with the company
     */
    public function contacts(): MorphToMany
    {
        return $this->morphToMany(\Modules\Contacts\Models\Contact::class, 'contactable')->withTimestamps()->orderBy('contactables.created_at');
    }

    /**
     * Get all of the notes for the company
     */
    public function notes(): MorphToMany
    {
        return $this->morphToMany(\Modules\Notes\Models\Note::class, 'noteable');
    }

    /**
     * Get all of the calls for the company
     */
    public function calls(): MorphToMany
    {
        return $this->morphToMany(\Modules\Calls\Models\Call::class, 'callable');
    }

    /**
     * Get the company owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Users\Models\User::class);
    }

    /**
     * Get the company industry
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Contacts\Models\Industry::class);
    }

    /**
     * Get the model display name
     */
    public function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }

    /**
     * Get the URL path
     */
    public function path(): Attribute
    {
        return Attribute::get(fn () => "/companies/{$this->id}");
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithResponseRelations(Builder $query): void
    {
        $query->withCount(['calls', 'notes'])->with([
            'parents',
            'media',
            'changelog',
            'changelog.pinnedTimelineSubjects',
            'contacts.phones', // for calling
            'deals.stage', 'deals.pipeline', 'deals.pipeline.stages' => function ($query) {
                return $query->orderBy('display_order');
            },
        ]);
    }

    /**
     * Purge the company data.
     */
    public function purge(): void
    {
        foreach (['contacts', 'emails', 'deals', 'activities', 'documents'] as $relation) {
            tap($this->{$relation}(), function ($query) {
                if ($query->getModel()->usesSoftDeletes()) {
                    $query->withTrashed();
                }

                $query->detach();
            });
        }

        $this->parents()->update(['parent_company_id' => null]);

        $this->notes->each->delete();
        $this->calls->each->delete();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }
}
