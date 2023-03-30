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

namespace App\Models;

use App\Enums\SyncState;
use App\Enums\EmailAccountType;
use App\Innoclapps\Concerns\HasMeta;
use App\Innoclapps\Contracts\Metable;
use App\Innoclapps\MailClient\Client;
use App\Innoclapps\Concerns\HasCreator;
use App\Innoclapps\Models\OAuthAccount;
use App\Innoclapps\Contracts\Primaryable;
use App\Support\Concerns\EmailAccountImap;
use App\Innoclapps\MailClient\ClientManager;
use App\Innoclapps\MailClient\ConnectionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EmailAccount extends Model
{
    use HasFactory;

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
     * @var \App\Innoclapps\MailClient\Client
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
        'create_contact'    => 'boolean',
        'initial_sync_from' => 'datetime',
        'last_sync_at'      => 'datetime',
        'smtp_port'         => 'int',
        'imap_port'         => 'int',
        'validate_cert'     => 'boolean',
        'password'          => 'encrypted',
        'sync_state'        => SyncState::class,
        'connection_type'   => ConnectionType::class,
        'user_id'           => 'int',
        'access_token_id'   => 'int',
        'sent_folder_id'    => 'int',
        'trash_folder_id'   => 'int',
        'created_by'        => 'int',
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
     * A model has OAuth connection
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function oAuthAccount()
    {
        return $this->hasOne(OAuthAccount::class, 'id', 'access_token_id');
    }

    /**
     * Check whether the user disabled the sync
     *
     * @return boolean
     */
    public function isSyncDisabled() : bool
    {
        return $this->sync_state === SyncState::DISABLED;
    }

    /**
     * Check whether the system disabled the sync
     *
     * @return boolean
     */
    public function isSyncStoppedBySystem() : bool
    {
        return $this->sync_state === SyncState::STOPPED;
    }

    /**
     * Checks whether an initial sync is performed for the syncable
     *
     * @return boolean
     */
    public function isInitialSyncPerformed() : bool
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function requiresAuth() : Attribute
    {
        return Attribute::get(function ($value) {
            return is_null($this->oAuthAccount) ? (bool) $value : $this->oAuthAccount->requires_auth;
        });
    }

    /**
     * An email account can belongs to a user (personal)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * An email account has many mail messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function messages()
    {
        return $this->hasMany(\App\Models\EmailAccountMessage::class);
    }

    /**
     * An email account has many mail folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function folders()
    {
        return $this->hasMany(\App\Models\EmailAccountFolder::class);
    }

    /**
     * An email account has sent folder indicator
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function sentFolder()
    {
        return $this->belongsTo(\App\Models\EmailAccountFolder::class);
    }

    /**
     * An email account has trash folder indicator
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function trashFolder()
    {
        return $this->belongsTo(\App\Models\EmailAccountFolder::class);
    }

    /**
     * Check whether the account is shared
     *
     * @return boolean
     */
    public function isShared() : bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check whether the account is personal
     *
     * @return boolean
     */
    public function isPersonal() : bool
    {
        return ! $this->isShared();
    }

    /**
     * Get the user type of the account
     *
     * 'shared' or 'personal'
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function type() : Attribute
    {
        return Attribute::get(
            fn () => $this->isShared() ? EmailAccountType::SHARED : EmailAccountType::PERSONAL
        );
    }

    /**
     * Check whether the account is primary account for the current logged in user
     *
     * @return boolean
     */
    public function isPrimary() : bool
    {
        return ((int) auth()->user()->getMeta(self::PRIMARY_META_KEY) === $this->id);
    }

    /**
     * Mark the account as primary for the given user
     */
    public function markAsPrimary(Metable & User $user) : static
    {
        $user->setMeta(self::PRIMARY_META_KEY, $this->id);

        return $this;
    }

    /**
     * Unmark the account as primary for the given user
     */
    public static function unmarkAsPrimary(Metable & User $user) : void
    {
        $user->removeMeta(self::PRIMARY_META_KEY);
    }

    /**
    * Get the account form name header option
    *
    * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    public function fromNameHeader() : Attribute
    {
        return Attribute::get(function () {
            $meta = $this->getMeta('from_name_header');

            return empty($meta) ? static::DEFAULT_FROM_NAME_HEADER : $meta;
        });
    }

    /**
     * Get the formatted from name header for the account
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function formattedFromNameHeader() : Attribute
    {
        return Attribute::get(function () {
            // When running the synchronization command via the console
            // there is no logged in user
            // In this case, we will just set as an empty name
            // as the SMTP client is not used in the synchronization command
            $name = auth()->check() ? auth()->user()->name : '';

            // Not work in Microsoft, cannot set custom from name header ATM
            if ($this->connection_type == ConnectionType::Outlook) {
                return $name;
            }

            return str_replace(
                ['{agent}', '{company}'],
                [$name, config('app.name')],
                $this->fromNameHeader
            );
        });
    }

    /**
     * Check whether the account can send mails
     *
     * @return boolean
     */
    public function canSendMails() : bool
    {
        return ! ($this->requires_auth || $this->isSyncStoppedBySystem());
    }

    /**
     * Create email account mail client
     *
     * @return \App\Innoclapps\MailClient\Client
     */
    public function createClient() : Client
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

        return tap($client, function ($instance) {
            $instance->setFromAddress($this->email)
                ->setFromName($this->formatted_from_name_header);
        });
    }

    /**
     * Get the account client
     *
     * @return \App\Innoclapps\MailClient\Client
     */
    public function getClient() : Client
    {
        if (! $this->clientInstance) {
            $this->clientInstance = $this->createClient();
        }

        return $this->clientInstance;
    }
}
