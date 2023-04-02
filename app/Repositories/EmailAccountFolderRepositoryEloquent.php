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

namespace App\Repositories;

use App\Models\EmailAccount;
use App\Models\EmailAccountFolder;
use App\Models\EmailAccountMessage;
use App\Innoclapps\Repository\AppRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Innoclapps\MailClient\ConnectionType;
use App\Innoclapps\MailClient\FolderCollection;
use App\Innoclapps\Contracts\Repositories\MediaRepository;
use App\Contracts\Repositories\EmailAccountFolderRepository;

class EmailAccountFolderRepositoryEloquent extends AppRepository implements EmailAccountFolderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return EmailAccountFolder::class;
    }

    /**
     * Get folders for account
     */
    public function getForAccount(int $account) : Collection
    {
        return $this->findWhere(['email_account_id' => $account]);
    }

    /**
     * Update folder for a given account
     */
    public function persistForAccount(EmailAccount $account, array $folder) : EmailAccountFolder
    {
        $parent = $this->updateOrCreate(
            $this->getUpdateOrCreateAttributes($account, $folder),
            array_merge($folder, [
                'email_account_id' => $account->id,
                'syncable'         => $folder['syncable'] ?? false,
            ])
        );

        $this->handleChildFolders($parent, $folder, $account);

        return $parent;
    }

    /**
     * Handle the child folders creation process
     */
    protected function handleChildFolders(EmailAccountFolder $parentFolder, array $folder, EmailAccount $account) : void
    {
        // Avoid errors if the children key is not set
        if (! isset($folder['children'])) {
            return;
        }

        if ($folder['children'] instanceof FolderCollection) {
            /**
             * @see \App\Listeners\CreateEmailAccountViaOAuth
             */
            $folder['children'] = $folder['children']->toArray();
        }

        foreach ($folder['children'] as $child) {
            $parent = $this->persistForAccount($account, array_merge($child, [
                'parent_id' => $parentFolder->id,
            ]));

            $this->handleChildFolders($parent, $child, $account);
        }
    }

    /**
     * Mark the folder as not selectable and syncable
     */
    public function markAsNotSelectable(int $id) : void
    {
        $this->update(['syncable' => false, 'selectable' => false], $id);
    }

    /**
     * Count the total unread messages for a given folder
     */
    public function countUnreadMessages(int $folderId) : int
    {
        return $this->countReadOrUnreadMessages($folderId, 0);
    }

    /**
     * Count the total read messages for a given folder
     */
    public function countReadMessages(int $folderId) : int
    {
        return $this->countReadOrUnreadMessages($folderId, 1);
    }

    /**
     * Boot the repository
     *
     * @return void
     */
    public static function boot()
    {
        static::deleting(function ($model, $repository) {
            $repository->purge($model);
        });
    }

    /**
     * Purge the folder data
     */
    protected function purge(EmailAccountFolder $folder) : void
    {
        // To prevent looping through all messages and loading them into
        // memory, we will get their id's only and purge the media
        // for the messages where media is available
        $messages = $folder->messages()->has('folders', '=', 1)->cursor()
            ->map(fn ($message) => $message->id);

        resolve(MediaRepository::class)
            ->purgeByMediableIds(EmailAccountMessage::class, $messages);

        $folder->messages()->has('folders', '=', 1)->delete();
    }

    /**
     * Count read or unread messages for a given folder
     */
    protected function countReadOrUnreadMessages(int $folderId, int $isRead) : int
    {
        return (int) $this->resetScope()
            ->resetCriteria()
            ->columns(['id'])
            ->withCount(['messages' => function ($query) use ($isRead) {
                return $query->where('is_read', $isRead);
            }])->findWhere(['id' => $folderId])->first()->messages_count ?? 0;
    }

    /**
     * Get the attributes that should be used for update or create method
     *
     * @param \App\Models\EmailAccount $account
     * @param array $folder
     *
     * @return array
     */
    protected function getUpdateOrCreateAttributes($account, $folder)
    {
        $attributes = ['email_account_id' => $account->id];

        // If the folder database ID is passed
        // use the ID as unique identifier for the folder
        if (isset($folder['id'])) {
            $attributes['id'] = $folder['id'];
        } else {
            // For imap account, we use the name as unique identifier
            // as the remote_id may not always be unique
            if ($account->connection_type === ConnectionType::Imap) {
                $attributes['name'] = $folder['name'];
            } else {
                // For API based accounts e.q. Gmail and Outlook
                // we use the remote_id as unique identifier
                $attributes['remote_id'] = $folder['remote_id'];
            }
        }

        return $attributes;
    }
}
