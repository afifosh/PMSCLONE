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

namespace App\Support\Concerns;

use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;

trait HasEmails
{
    /**
     * Get all of the emails for the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function emails()
    {
        return $this->morphToMany(
            \App\Models\EmailAccountMessage::class,
            'messageable',
            'email_account_messageables',
            null,
            'message_id'
        );
    }

    /**
     * A model has unread emails
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function unreadEmails()
    {
        return $this->emails()->where('is_read', false)->whereHas('folders', function ($folderQuery) {
            return $folderQuery->where('syncable', true);
        });
    }

    /**
     * Get the unread emails that the user can see
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function unreadEmailsForUser()
    {
        return $this->unreadEmails()->where(function ($query) {
            $query->whereHas('account', function ($accountQuery) {
                EmailAccountsForUserCriteria::applyQuery($accountQuery);
            })->whereHas('folders.account', function ($query) {
                return $query->whereColumn('folder_id', '!=', 'trash_folder_id');
            });
        });
    }
}
