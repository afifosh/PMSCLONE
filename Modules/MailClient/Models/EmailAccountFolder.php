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

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Concerns\HasMeta;
use Modules\Core\Contracts\Metable;
use Modules\Core\Models\Media;
use Modules\Core\Models\Model;
use Modules\MailClient\Client\ConnectionType;
use Modules\MailClient\Client\FolderIdentifier;
use Modules\MailClient\Database\Factories\EmailAccountFolderFactory;
use Modules\MailClient\Support\EmailAccountFolderCollection;

class EmailAccountFolder extends Model implements Metable
{
    use HasMeta, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name', 'display_name', 'remote_id',
        'email_account_id', 'syncable', 'selectable', 'type', 'support_move',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'selectable' => 'boolean',
        'syncable' => 'boolean',
        'support_move' => 'boolean',
        'parent_id' => 'int',
        'email_account_id' => 'int',
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
     * A folder belongs to email account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(\Modules\MailClient\Models\EmailAccount::class, 'email_account_id');
    }

    /**
     * A folder belongs to email account
     */
    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\MailClient\Models\EmailAccountMessage::class,
            'email_account_message_folders',
            'folder_id',
            'message_id'
        );
    }

    /**
     * Get the display name attribute
     *
     * The function check if there is no translation found
     * for the labels, returns the original stored value
     */
    public function displayName(): Attribute
    {
        return Attribute::get(function ($value) {
            $customLangKey = 'custom.mail.labels.'.$value;
            $primaryLangKey = 'mailclient::mail.labels.'.$value;

            if (Lang::has($customLangKey)) {
                return __($customLangKey);
            } elseif (Lang::has($primaryLangKey)) {
                return __($primaryLangKey);
            }

            return $value;
        });
    }

    /**
     * Get the folder identifier
     */
    public function identifier(): FolderIdentifier
    {
        if ($this->account->connection_type === ConnectionType::Imap) {
            return new FolderIdentifier('name', $this->name);
        }

        return new FolderIdentifier('id', $this->remote_id);
    }

    /**
     * Mark the folder as not selectable and syncable
     */
    public function markAsNotSelectable(): static
    {
        $this->fill(['syncable' => false, 'selectable' => false])->save();

        return $this;
    }

    /**
     * Count the total unread messages for a given folder
     */
    public function countUnreadMessages(): int
    {
        return $this->countReadOrUnreadMessages($this->id, 'unread');
    }

    /**
     * Count the total read messages for a given folder
     */
    public function countReadMessages(): int
    {
        return $this->countReadOrUnreadMessages($this->id, 'read');
    }

    /**
     * Count read or unread messages for a given folder
     */
    protected function countReadOrUnreadMessages(int $folderId, string $scope): int
    {
        return (int) static::select('id')
            ->withCount(['messages' => function ($query) use ($scope) {
                return $query->{$scope}();
            }])->where('id', $folderId)->first()->messages_count ?? 0;
    }

    /**
     * Purge the folder data
     */
    public function purge(): void
    {
        // To prevent looping through all messages and loading them into
        // memory, we will get their id's only and purge the media
        // for the messages where media is available
        $messages = $this->messages()->has('folders', '=', 1)->cursor()
            ->each(function ($message) {
                foreach (['deals', 'contacts', 'companies'] as $relation) {
                    tap($message->{$relation}(), function ($query) {
                        if ($query->getModel()->usesSoftDeletes()) {
                            $query->withTrashed();
                        }

                        $query->detach();
                    });
                }
            })
            ->map(fn ($message) => $message->id);

        (new Media())->purgeByMediableIds(EmailAccountMessage::class, $messages);

        $this->messages()->has('folders', '=', 1)->delete();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return (new EmailAccountFolderCollection($models))->sortByType();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): EmailAccountFolderFactory
    {
        return EmailAccountFolderFactory::new();
    }
}
