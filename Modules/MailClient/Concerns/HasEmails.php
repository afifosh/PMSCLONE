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

namespace Modules\MailClient\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\MailClient\Criteria\EmailAccountsForUserCriteria;

/** @mixin \Modules\Core\Models\Model */
trait HasEmails
{
    /**
     * Get all of the emails for the contact.
     */
    public function emails(): MorphToMany
    {
        return $this->morphToMany(
            \Modules\MailClient\Models\EmailAccountMessage::class,
            'messageable',
            'email_account_messageables',
            null,
            'message_id'
        );
    }

    /**
     * A model has unread emails
     */
    public function unreadEmails(): MorphToMany
    {
        return $this->emails()->unread()->whereHas('folders', function ($folderQuery) {
            return $folderQuery->where('syncable', true);
        });
    }

    /**
     * Get the unread emails that the user can see
     */
    public function unreadEmailsForUser(): MorphToMany
    {
        return $this->unreadEmails()->where(function ($query) {
            $query->whereHas('account', function ($accountQuery) {
                $accountQuery->criteria(EmailAccountsForUserCriteria::class);
            })->whereHas('folders.account', function ($query) {
                return $query->whereColumn('folder_id', '!=', 'trash_folder_id');
            });
        });
    }
}
