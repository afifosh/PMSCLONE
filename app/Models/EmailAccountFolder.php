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

use App\Innoclapps\Models\Model;
use App\Innoclapps\Concerns\HasMeta;
use Illuminate\Support\Facades\Lang;
use App\Innoclapps\Contracts\Metable;
use App\Innoclapps\MailClient\ConnectionType;
use App\Support\EmailAccountFolderCollection;
use App\Innoclapps\MailClient\FolderIdentifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailAccountFolder extends Model implements Metable
{
    use HasMeta, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name', 'display_name', 'remote_id', 'email_account_id', 'syncable', 'selectable', 'type', 'support_move',
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'selectable'       => 'boolean',
        'syncable'         => 'boolean',
        'support_move'     => 'boolean',
        'parent_id'        => 'int',
        'email_account_id' => 'int',
    ];

    /**
     * A folder belongs to email account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\EmailAccount::class, 'email_account_id');
    }

    /**
     * A folder belongs to email account
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function messages()
    {
        return $this->belongsToMany(
            \App\Models\EmailAccountMessage::class,
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function displayName() : Attribute
    {
        return Attribute::get(function ($value) {
            $customKey = 'custom.mail.labels.' . $value;
            $primaryCustomKey = 'mail.labels.' . $value;

            if (Lang::has($customKey)) {
                return __($customKey);
            } elseif (Lang::has($primaryCustomKey)) {
                return __($primaryCustomKey);
            }

            return $value;
        });
    }

    /**
     * Get the folder identifier
     *
     * @return \App\Innoclapps\MailClient\FolderIdentifier
     */
    public function identifier() : FolderIdentifier
    {
        if ($this->account->connection_type === ConnectionType::Imap) {
            return new FolderIdentifier('name', $this->name);
        }

        return new FolderIdentifier('id', $this->remote_id);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return (new EmailAccountFolderCollection($models))->sortByType();
    }
}
