<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Modules\Core\Concerns\HasAvatar;
use Modules\Core\Contracts\Presentable;
use Modules\Core\Media\HasMedia;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Resourceable;
use Modules\Core\Timeline\Timelineable;
use Modules\MailClient\Support\EmailAccountMessageBody;

class EmailAccountMessage extends Model implements Presentable
{
    use HasAvatar,
        HasMedia,
        Resourceable,
        Timelineable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_account_id', 'remote_id', 'message_id',
        'subject', 'html_body', 'text_body', 'is_read',
        'is_draft', 'date', 'is_sent_via_app', 'hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Proper casts must be added to ensure the isDirty() method works fine
     * when checking whether the message is updated to broadcast to the front-end via sync
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'is_draft' => 'boolean',
        'is_read' => 'boolean',
        'is_sent_via_app' => 'boolean',
        'email_account_id' => 'int',
        'clicks' => 'int',
        'clicked_at' => 'datetime',
        'opens' => 'int',
        'opened_at' => 'datetime',
    ];

    /**
     * The columns for the model that are searchable.
     */
    protected static array $searchableColumns = [
        'subject' => 'like',
        'from.address',
        'from.name',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->purge();
        });
    }

    /**
     * A messages belongs to email account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(\Modules\MailClient\Models\EmailAccount::class, 'email_account_id');
    }

    /**
     * A message belongs to many folders
     */
    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\MailClient\Models\EmailAccountFolder::class,
            'email_account_message_folders',
            'message_id',
            'folder_id'
        )
            ->using(\Modules\MailClient\Models\EmailAccountMessageFolder::class);
    }

    /**
     * A message has many addresses
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(\Modules\MailClient\Models\EmailAccountMessageAddress::class, 'message_id');
    }

    /**
     * A message can have many contacts.
     */
    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(
            \Modules\Contacts\Models\Contact::class,
            'messageable',
            'email_account_messageables',
            'message_id'
        );
    }

    /**
     * A message can have many companies.
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(
            \Modules\Contacts\Models\Company::class,
            'messageable',
            'email_account_messageables',
            'message_id'
        );
    }

    /**
     * A message can have many deals.
     */
    public function deals(): MorphToMany
    {
        return $this->morphedByMany(
            \Modules\Deals\Models\Deal::class,
            'messageable',
            'email_account_messageables',
            'message_id'
        );
    }

    /**
     * A message from address
     */
    public function from(): HasOne
    {
        return $this->hasOne(\Modules\MailClient\Models\EmailAccountMessageAddress::class, 'message_id')
            ->where('address_type', 'from');
    }

    /**
     * A message sender address
     */
    public function sender(): HasOne
    {
        return $this->hasOne(\Modules\MailClient\Models\EmailAccountMessageAddress::class, 'message_id')
            ->where('address_type', 'sender');
    }

    /**
     * A message to address
     */
    public function to(): HasMany
    {
        return $this->addresses()->where('address_type', 'to');
    }

    /**
     * A message cc address
     */
    public function cc(): HasMany
    {
        return $this->addresses()->where('address_type', 'cc');
    }

    /**
     * A message bcc address
     */
    public function bcc(): HasMany
    {
        return $this->addresses()->where('address_type', 'bcc');
    }

    /**
     * A message replyTo address
     */
    public function replyTo(): HasMany
    {
        return $this->addresses()->where('address_type', 'replyTo');
    }

    /**
     * A message has many headers
     */
    public function headers(): HasMany
    {
        return $this->hasMany(\Modules\MailClient\Models\EmailAccountMessageHeader::class, 'message_id');
    }

    /**
     * A message has many link clicks
     */
    public function linksClicks(): HasMany
    {
        return $this->hasMany(\Modules\MailClient\Models\MessageLinksClick::class, 'message_id');
    }

    /**
     * Get the model display name
     */
    public function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->subject);
    }

    /**
     * Get the URL path
     */
    public function path(): Attribute
    {
        return Attribute::get(function () {
            $accountId = $this->email_account_id;
            $folderId = $this->folders->first()->getKey();
            $messageId = $this->getKey();

            return "/inbox/$accountId/folder/$folderId/messages/$messageId";
        });
    }

    /**
     * Get the timeline sort column.
     */
    public function getTimelineSortColumn(): string
    {
        return 'date';
    }

    /**
     * Get the message attachments excluding the inline
     */
    public function attachments(): MorphToMany
    {
        return static::media()->wherePivot('tag', '!=', 'embedded-attachments');
    }

    /**
     * Get the message inline attachments
     */
    public function inlineAttachments(): MorphToMany
    {
        return static::media()->wherePivot('tag', '=', 'embedded-attachments');
    }

    /**
     * Determine if the message is a reply
     */
    public function isReply(): bool
    {
        return ! is_null($this->headers->firstWhere('name', 'in-reply-to'))
            || ! is_null($this->headers->firstWhere('name', 'references'));
    }

    /**
     * Get the previewText attribute
     */
    public function previewText(): Attribute
    {
        return Attribute::get(fn () => $this->body()->previewText());
    }

    /**
     * Get the visibleText attribute without any quoted content
     *
     * NOTE: Sometimes the EmailParser may fail because it won't be able
     * to recognize the quoted text and will return empty message
     * In this case, just return the original preview text
     */
    public function visibleText(): Attribute
    {
        return Attribute::get(fn () => $this->body()->visibleText());
    }

    /**
     * Get the hiddenText attribute
     */
    public function hiddenText(): Attribute
    {
        return Attribute::get(fn () => $this->body()->hiddenText());
    }

    /**
     * Get the message body
     */
    public function body(): EmailAccountMessageBody
    {
        return once(function () {
            return new EmailAccountMessageBody($this);
        });
    }

    /**
     * Get the relation name when the model is used as activity
     */
    public function getTimelineRelation(): string
    {
        return 'emails';
    }

    /**
     * Mark a message as read
     */
    public function markAsRead(?int $folderId = null): static
    {
        if ($this->is_read) {
            return $this;
        }

        $folders = $folderId ?
        [EmailAccountFolder::with('account')->find($folderId)] :
        $this->folders->loadMissing('account');

        $client = $this->account->createClient();

        foreach ($folders as $folder) {
            $client->getMessage($this->remote_id, $folder->identifier())->markAsRead();
        }

        $this->fill(['is_read' => true])->save();

        return $this;
    }

    /**
     * Mark a message as unread
     */
    public function markAsUnread(?int $folderId = null): static
    {
        if (! $this->is_read) {
            return $this;
        }

        $folders = $folderId ?
        [EmailAccountFolder::with('account')->find($folderId)] :
        $this->folders->loadMissing('account');

        $client = $this->account->createClient();

        foreach ($folders as $folder) {
            $client->getMessage($this->remote_id, $folder->identifier())->markAsUnread();
        }

        $this->fill(['is_read' => false])->save();

        return $this;
    }

    /**
     * Purge the message data.
     */
    public function purge(): void
    {
        // foreach (['deals', 'contacts', 'companies', 'folders'] as $relation) {
        foreach (['contacts', 'folders'] as $relation) {
            tap($this->{$relation}(), function ($query) {
                if ($query->getModel()->usesSoftDeletes()) {
                    $query->withTrashed();
                }

                $query->detach();
            });
        }
    }

    /**
     * Scope a query to include only unread messages.
     */
    public function scopeUnread(Builder $query)
    {
        // is_read = 0 causes slow performance, use is_read < 1 for better performance
        $query->where('is_read', '<', 1);
    }

    /**
     * Scope a query to include only read messages.
     */
    public function scopeRead(Builder $query)
    {
        // is_read = 1 causes slow performance, use is_read > 0 for better performance
        $query->where('is_read', '>', 0);
    }

    /**
     * Scope a query to include only messages of the given folder.
     */
    public function scopeOfFolder(Builder $query, int|string $folderId): void
    {
        $query->whereHas('folders', function ($query) use ($folderId) {
            return $query->where('folder_id', $folderId);
        });
    }

    /**
     * Apply where in remote ids
     *
     * @see https://stackoverflow.com/questions/53683774/eloquent-delete-sqlstate22007-invalid-datetime-format-1292-truncated-incor
     */
    public function scopeWhereRemoteIdsIn(Builder $query, array $remoteIds): void
    {
        $query->whereIn('remote_id', Arr::valuesAsString($remoteIds));
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithCommon(Builder $query): void
    {
        $query->with([
            'headers',
            'from',
            'sender',
            'to',
            'cc',
            'bcc',
            'replyTo',
            'attachments',
            'folders',
            'account',
            'account.folders' => fn ($query) => $query->withCount([
              'messages as unread_count' => fn ($query) => $query->unread(),
            ]),
            // 'contacts.nextActivity',
            // 'companies.nextActivity',
            // 'deals.nextActivity',
        ]);
    }

    /**
     * Get the activity front-end component
     */
    public function getTimelineComponent(): string
    {
        return 'record-tab-timeline-email';
    }

    public function getParent()
    {
        $parentId = null;

        // First, check the "In-Reply-To" header
        $inReplyToHeader = $this->headers->firstWhere('name', 'in-reply-to');
        if ($inReplyToHeader) {
            $parentId = $inReplyToHeader->value;
        }

        // If the "In-Reply-To" header is not present or empty, check the "References" header
        if (!$parentId) {
            $referencesHeader = $this->headers->firstWhere('name', 'references');
            if ($referencesHeader) {
                $referenceIds = explode(',', $referencesHeader->value);
                $parentId = end($referenceIds); // Get the last message ID in the References header
            }
        }

        // If a parent ID was found, retrieve the parent email
        if ($parentId) {
            $message= EmailAccountMessage::where('message_id', $parentId)->first();
            // $messages=new EmailAccountMessageRepositoryEloquent;
            // if($message)
            // $message->loadMissing();
            return $message;
        }

        // If no parent ID was found, return null
        return null;
    }



    public function getThread(){

        $threadMessage = collect();

        // Keep looping until we find a message that is not a reply
        $parent = $this->getParent();
        while ($parent && $parent->isReply()) {
            $threadMessage->prepend($parent);
            $parent = $parent->getParent();
        }

        // If we found a message that is not a reply, add it to the thread
        if ($parent) {
            $threadMessage->prepend($parent);
        }

        return $threadMessage;
    }
}
