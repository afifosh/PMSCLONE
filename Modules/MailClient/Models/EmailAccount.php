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

namespace Modules\MailClient\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\Concerns\HasCreator;
use Modules\Core\Concerns\HasMeta;
use Modules\Core\Contracts\Metable;
use Modules\Core\Contracts\Primaryable;
use Modules\Core\Models\Media;
use Modules\Core\Models\Model;
use Modules\Core\Models\OAuthAccount;
use Modules\Core\Synchronization\SyncState;
use Modules\MailClient\Client\Client;
use Modules\MailClient\Client\ClientManager;
use Modules\MailClient\Client\ConnectionType;
use Modules\MailClient\Concerns\EmailAccountImap;
use Modules\MailClient\Criteria\EmailAccountsForUserCriteria;
use Modules\MailClient\Database\Factories\EmailAccountFactory;
use Modules\MailClient\Enums\EmailAccountType;
// use Modules\Users\Models\User;
use App\Models\Admin as User;
use App\Models\Admin;
use Modules\Core\Models\Permission;

class EmailAccount extends Model implements Metable, Primaryable
{
    use HasMeta,
        HasCreator,
        HasFactory,
        EmailAccountImap;

    /**
     * Indicates the primary meta key for user
     */
    const PRIMARY_META_KEY = 'primary-email-account';

    /**
     * The default shared account from header type
     */
    const DEFAULT_FROM_NAME_HEADER = '{agent} from {company}';

    /**
     * Client instance cached property
     *
     * @var \Modules\MailClient\Client\Client
     */
    protected $clientInstance;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'create_contact' => 'boolean',
        'initial_sync_from' => 'datetime',
        'last_sync_at' => 'datetime',
        'smtp_port' => 'int',
        'imap_port' => 'int',
        'validate_cert' => 'boolean',
        'password' => 'encrypted',
        'sync_state' => SyncState::class,
        'connection_type' => ConnectionType::class,
        'user_id' => 'int',
        'access_token_id' => 'int',
        'sent_folder_id' => 'int',
        'trash_folder_id' => 'int',
        'created_by' => 'int',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'connection_type',
        'last_sync_at', 'requires_auth', 'initial_sync_from',
        'sent_folder_id', 'trash_folder_id', 'create_contact',
        // imap
        'validate_cert', 'username',
        'imap_server', 'imap_port', 'imap_encryption',
        'smtp_server', 'smtp_port', 'smtp_encryption',
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
     * A model has OAuth connection
     */
    public function oAuthAccount(): HasOne
    {
        return $this->hasOne(OAuthAccount::class, 'id', 'access_token_id');
    }

    /**
     * Check whether the user disabled the sync
     */
    public function isSyncDisabled(): bool
    {
        return $this->sync_state === SyncState::DISABLED;
    }

    /**
     * Check whether the system disabled the sync
     */
    public function isSyncStoppedBySystem(): bool
    {
        return $this->sync_state === SyncState::STOPPED;
    }

    /**
     * Checks whether an initial sync is performed for the syncable
     */
    public function isInitialSyncPerformed(): bool
    {
        return ! empty($this->last_sync_at);
    }

    /**
     * We will set custom accessor for the requires_auth attribute
     *
     * If it's OAuthAccount we will return the value from the actual oauth account
     * instead of the syncable value, because OAuth account can be used in other features
     * in the application and these features may update the requires_auth on the oauth_accounts table directly
     * In this case, we this will ensure that the requires_auth attribute value is up to date
     *
     * If it's regular account without OAuth e.q. IMAP, we will return the value from the actual syncable model
     */
    public function requiresAuth(): Attribute
    {
        return Attribute::get(function ($value) {
            return is_null($this->oAuthAccount) ? (bool) $value : $this->oAuthAccount->requires_auth;
        });
    }

    public function users()
    {
        return $this->belongsToMany(Admin::class, 'user_email_accounts', 'email_account_id', 'user_id')
        ->withPivot('permission_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_email_accounts');
    }

    /**
     * An email account can belongs to a user (personal)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * An email account has many mail messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(\Modules\MailClient\Models\EmailAccountMessage::class);
    }

    /**
     * An email account has many mail folders
     */
    public function folders(): HasMany
    {
        return $this->hasMany(\Modules\MailClient\Models\EmailAccountFolder::class);
    }

    /**
     * An email account has sent folder indicator
     */
    public function sentFolder(): BelongsTo
    {
        return $this->belongsTo(\Modules\MailClient\Models\EmailAccountFolder::class);
    }

    /**
     * An email account has trash folder indicator
     */
    public function trashFolder(): BelongsTo
    {
        return $this->belongsTo(\Modules\MailClient\Models\EmailAccountFolder::class);
    }

    /**
     * Check whether the account is shared
     */
    public function isShared(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check whether the account is personal
     */
    public function isPersonal(): bool
    {
        return ! $this->isShared();
    }

    /**
     * Get the user type of the account
     *
     * 'shared' or 'personal'
     */
    public function type(): Attribute
    {
        return Attribute::get(
            fn () => $this->isShared() ? EmailAccountType::SHARED : EmailAccountType::PERSONAL
        );
    }

    /**
     * Check whether the account is primary account for the current logged in user
     */
    public function isPrimary(): bool
    {
        return (int) auth()->user()->getMeta(self::PRIMARY_META_KEY) === $this->id;
    }

    /**
     * Mark the account as primary for the given user
     */
    public function markAsPrimary(Metable&User $user): static
    {
        $user->setMeta(self::PRIMARY_META_KEY, $this->id);

        return $this;
    }

    /**
     * Unmark the account as primary for the given user
     */
    public static function unmarkAsPrimary(Metable&User $user): void
    {
        $user->removeMeta(self::PRIMARY_META_KEY);
    }

    /**
     * Get the account form name header option
     */
    public function fromNameHeader(): Attribute
    {
        return Attribute::get(function () {
            $meta = $this->getMeta('from_name_header');

            return empty($meta) ? static::DEFAULT_FROM_NAME_HEADER : $meta;
        });
    }

    /**
     * Get the formatted from name header for the account
     */
    public function formattedFromNameHeader(): Attribute
    {
        return Attribute::get(function () {
            // When running the synchronization command via the console
            // there is no logged in user
            // In this case, we will just set as an empty name
            // as the SMTP client is not used in the synchronization command
            if ($this->isPersonal()) {
                $agent = $this->user->name;
            } else {
                $agent = auth()->check() ? auth()->user()->name : '';
            }

            // Does not work in Microsoft, cannot set custom from name header ATM
            if ($this->connection_type == ConnectionType::Outlook) {
                return $agent;
            }

            return $this->fromNameHeader;
            return $this->formatFromNameHeader($this->fromNameHeader, $agent);
        });
    }

    /**
     * Format the given from name header
     */
    public function formatFromNameHeader(string $value, string $agent): string
    {
        return str_replace(
            ['{agent}', '{company}'],
            [$agent, config('app.name')],
            $value
        );
    }

    /**
     * Check whether the account can send mails
     */
    public function canSendMails(): bool
    {
        return ! ($this->requires_auth || $this->isSyncStoppedBySystem());
    }

    /**
     * Create email account mail client
     */
    public function createClient(): Client
    {
        if ($this->oAuthAccount) {
            $client = ClientManager::createClient(
                $this->connection_type,
                $this->oAuthAccount->tokenProvider()
            );
        } else {
            $client = ClientManager::createClient(
                $this->connection_type,
                $this->getImapConfig(),
                $this->getSmtpConfig()
            );
        }

        $client->setFromAddress($this->email)->setFromName($this->formatted_from_name_header);

        return $client;
    }

    /**
     * Get the account client
     */
    public function getClient(): Client
    {
        if (! $this->clientInstance) {
            $this->clientInstance = $this->createClient();
        }

        return $this->clientInstance;
    }

    /**
     * Scope a query to include only syncable accounts.
     */
    public function scopeSyncable(Builder $query): void
    {
        $query->where('sync_state', SyncState::ENABLED);
    }

    /**
     * Scope a query to include only shared accounts.
     */
    public function scopeShared(Builder $query): void
    {
        $query->doesntHave('user');
    }

    /**
     * Scope a query to include only personal accounts.
     */
    public function scopePersonal(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Create a scope to eager load the account folders data.
     */
    public function scopeWithFolders(Builder $query): void
    {
        $query->with(['folders' => fn ($query) => $query->withCount([
            'messages as unread_count' => fn ($query) => $query->where('is_read', false),
        ])]);
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithResponseRelations(Builder $query): void
    {
        $query->withCount([
            'messages as unread_count' => fn ($query) => $query->where('is_read', false),
        ])->withFolders()->with([
            'user',
            'sentFolder',
            'trashFolder',
            'oAuthAccount',
        ]);
    }

    /**
     * Set that this account requires authentication
     */
    public function setRequiresAuthentication(bool $value = true): static
    {
        if (! is_null($this->oAuthAccount)) {
            $this->oAuthAccount->setRequiresAuthentication($value);
        }

        $this->requires_auth = $value;

        $this->save();

        return $this;
    }

    /**
     * Set the account synchronization state.
     */
    public function setSyncState(SyncState $state, ?string $comment = null): static
    {
        $this->sync_state = $state;
        $this->sync_state_comment = $comment;
        $this->save();

        return $this;
    }

    /**
     * Enable account synchronization
     */
    public function enableSync(): static
    {
        return $this->setSyncState(SyncState::ENABLED);
    }

    /**
     * Count the unread messages for all accounts the given user can access
     */
    public static function countUnreadMessagesForUser(User|int $user): int
    {
        /** @var int */
        $result = static::select('id')
            ->criteria(new EmailAccountsForUserCriteria($user))
            ->withCount(['messages' => function ($query) {
                return $query->where('is_read', 0)
                    ->whereHas('folders', fn ($folderQuery) => $folderQuery->where('syncable', true));
            }])
            ->groupBy('id')
            ->get()
            ->reduce(fn ($carry, $item) => $carry + $item['messages_count'], 0);

        return $result;
    }

    /**
     * Count the total unread messages for the given account.
     */
    public static function countUnreadMessages(int $id): int
    {
        return static::countReadOrUnreadMessages($id, false);
    }

    /**
     * Count the total read messages for the given account.
     */
    public static function countReadMessages(int $id): int
    {
        return static::countReadOrUnreadMessages($id, true);
    }

    /**
     * Count read or unread messages for the given account.
     */
    protected static function countReadOrUnreadMessages(int $id, int|bool $isRead): int
    {
        return EmailAccountMessage::where('email_account_id', $id)
            ->where('is_read', $isRead)
            ->count();
    }

    /**
     * Purge the model data.
     */
    public function purge(): void
    {
        // Detach from only messages with associations
        // This helps to not loop over all messages and delete them
        foreach (['contacts', 'companies'] as $relation) {
            $this->messages()->whereHas($relation, function ($query) {
                $query->withTrashed();
            })->cursor()->each(function ($message) use ($relation) {
                $message->{$relation}()->withTrashed()->detach();
            });
        }

        // To prevent looping through all messages and loading them into
        // memory, we will get their id's only and purge the media for the messages where media is available
        $messagesIds = $this->messages()->cursor()->map(fn ($message) => $message->id);

        (new Media())->purgeByMediableIds(EmailAccountMessage::class, $messagesIds);

        $this->messages()->delete();

        $this->folders->each->delete();

        $systemEmailAccountId = settings('system_email_account_id');

        if ((int) $systemEmailAccountId === (int) $this->id) {
            settings()->forget('system_email_account_id')->save();
        }
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): EmailAccountFactory
    {
        return EmailAccountFactory::new();
    }
}
