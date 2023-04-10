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

use App\Enums\SyncState;
use App\Models\EmailAccount;
use App\Models\EmailAccountMessage;
use App\Innoclapps\Contracts\Metable;
use App\Innoclapps\Repository\AppRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\Contracts\Repositories\MediaRepository;
use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;
use App\Contracts\Repositories\EmailAccountFolderRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Innoclapps\Contracts\Repositories\OAuthAccountRepository;
use App\Models\Admin;
use Exception;

class EmailAccountRepositoryEloquent extends AppRepository implements EmailAccountRepository
{
    /**
    * @var \App\Contracts\Repositories\EmailAccountFolderRepository
    */
    protected $folderRepository;

    /**
    * Specify Model class name
    *
    * @return string
    */
    public static function model()
    {
        return EmailAccount::class;
    }

    /**
    * Perform model insert operation
    *
    * @param \Illuminate\Database\Eloquent\Model $model
    * @param array $attributes
    *
    * @return void
    */
    protected function performInsert($model, $attributes)
    {
        if(!isset($attributes['create_contact'])){
            $attributes['create_contact']=false;
        }
        // If user exists, mark the account as personal before insert
        if (isset($attributes['user_id'])) {
            $model->forceFill(['user_id' => $attributes['user_id']]);
        }
        else{
            $model->forceFill(['user_id' => auth()->user()->id]);
        }

        parent::performInsert($model, $attributes);

        $model->setMeta(
            'from_name_header',
            ($attributes['from_name_header'] ?? '') ?: EmailAccount::DEFAULT_FROM_NAME_HEADER
        );
        foreach ($attributes['folders'] ?? [] as $folder) {

            if(gettype($folder)=="string")
            {
            $folder=json_decode($folder,true);
            }
            
            if(gettype($folder)=="array"){
                $this->getFolderRepository()->persistForAccount($model, $folder);
        }
        }

        foreach (['trash', 'sent'] as $folderType) {
            if ($folder = $model->folders->firstWhere('type', $folderType)) {
                tap($model, function ($instance) use ($folder, $folderType) {
                    $instance->{$folderType . 'Folder'}()->associate($folder);
                })->save();
            }
        }
    }

    /**
    * Update email account
    *
    * @param array $attributes
    * @param mixed $id
    *
    * @return \App\Models\EmailAccountFolder
    */
    public function update(array $attributes, $id)
    {
        if(!isset($attributes['create_contact'])){
            $attributes['create_contact']=false;
        }
        $account = parent::update($attributes, $id);

        if (isset($attributes['from_name_header'])) {
            $account->setMeta('from_name_header', $attributes['from_name_header']);
        }


        if(isset($attributes['folders'])){
            $folders=$this->getFolderRepository()->getForAccount($account->id);
            foreach($folders as $folder){
                $this->getFolderRepository()->markAsNotSelectable($folder->id);
            }    
        foreach ($attributes['folders'] as $folder) {
            if(gettype($folder)=="string")
            {
            $folder=json_decode($folder,true);
            }
            if(gettype($folder)=="array"){
                $this->getFolderRepository()->persistForAccount($account, $folder);
        }
        }
    }
        return $account;
    }

    /**
    * Set the account synchronization state
    *
    * @param int $id
    * @param \App\Enums\SyncState $state
    * @param string|null $comment
    *
    * @return void
    */
    public function setSyncState($id, SyncState $state, ?string $comment = null) : void
    {
        EmailAccount::unguarded(function () use ($id, $state, $comment) {
            $this->update(['sync_state' => $state, 'sync_state_comment' => $comment], $id);
        });
    }

    /**
    * Enable account synchronization
    */
    public function enableSync(int $id) : void
    {
        $this->setSyncState($id, SyncState::ENABLED);
    }

    /**
    * Get syncable email accounts
    */
    public function getSyncable() : Collection
    {
        return $this->orderBy('email', 'asc')->findByField('sync_state', SyncState::ENABLED);
    }

    /**
    * Count the unread messages for all accounts the given user can access
    */
    public function countUnreadMessagesForUser(Admin|int $user) : int
    {
        /** @var int */
        $result = $this->columns('id')
            ->resetScope()
            ->resetCriteria()
            ->pushCriteria(new EmailAccountsForUserCriteria($user))
            ->groupBy('id')
            ->withCount(['messages' => function ($query) {
                return $query->where('is_read', 0)
                    ->whereHas('folders', fn ($folderQuery) => $folderQuery->where('syncable', true));
            }])
            ->all()
            ->reduce(fn ($carry, $item) => $carry + $item['messages_count'], 0);

        $this->popCriteria(EmailAccountsForUserCriteria::class);

        return $result;
    }

    /**
    * Count the total unread messages for a given account
    */
    public function countUnreadMessages(int $accountId) : int
    {
        return $this->countReadOrUnreadMessages($accountId, 0);
    }

    /**
    * Count the total read messages for a given account
    */
    public function countReadMessages(int $accountId) : int
    {
        return $this->countReadOrUnreadMessages($accountId, 1);
    }

    /**
    * Get all shared email accounts
    */
    public function getShared() : Collection
    {
        return $this->doesntHave('user')
            ->orderBy('email', 'asc')
            ->all();
    }

    /**
    * Get all user personal email accounts
    */
    public function getPersonal(int $userId) : Collection
    {
        return $this->orderBy('email', 'asc')->findWhere(['user_id' => $userId]);
    }

    /**
    * Set that this account requires authentication
    */
    public function setRequiresAuthentication(int $id, bool $value = true) : void
    {
        $account = $this->find($id);

        if (! is_null($account->oAuthAccount)) {
            resolve(OAuthAccountRepository::class)->update(
                ['requires_auth' => $value],
                $account->oAuthAccount->id
            );
        }

        $this->update(['requires_auth' => $value], $account->id);
    }

    /**
    * Mark the given account as primary for the given user
    */
    public function markAsPrimary(EmailAccount $account, Metable & Admin $user) : void
    {
        $account->markAsPrimary($user);
    }

    /**
    * Remove primary account
    */
    public function removePrimary(Metable & Admin $user) : void
    {
        EmailAccount::unmarkAsPrimary($user);
    }

    /**
    * Find email account by email addresss
    */
    public function findByEmail(string $email) : ?EmailAccount
    {
        return $this->findByField('email', $email)->first();
    }

    /**
    * Count read or unread messages for a given account
    */
    protected function countReadOrUnreadMessages(int $accountId, int $isRead) : int
    {
        return resolve(EmailAccountMessageRepository::class)->resetScope()
            ->resetCriteria()
            ->count(['email_account_id' => $accountId, 'is_read' => $isRead], 'id');
    }

    /**
    * Get the folder repository
    */
    public function getFolderRepository() : EmailAccountFolderRepository
    {
        if (! is_null($this->folderRepository)) {
            return $this->folderRepository;
        }

        return $this->folderRepository = resolve(EmailAccountFolderRepository::class);
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
    * Purge the account data
    */
    protected function purge(EmailAccount $account) : void
    {
        // Detach from only messages with associations
        // This helps to not loop over all messages and delete them
        foreach (['contacts', 'companies'] as $relation) {
            $account->messages()->whereHas($relation)->cursor()->each(function ($message) use ($relation) {
                $message->{$relation}()->detach();
            });
        }
        // To prevent looping through all messages and loading them into
        // memory, we will get their id's only and purge the media for the messages where media is available
        $messagesIds = $account->messages()->cursor()->map(fn ($message) => $message->id);

        resolve(MediaRepository::class)
            ->purgeByMediableIds(EmailAccountMessage::class, $messagesIds);

        $account->messages()->delete();

        $this->getFolderRepository()->delete($account->folders);

        // $systemEmailAccountId = settings('system_email_account_id');

        // if ((int) $systemEmailAccountId === (int) $account->id) {
        //     settings()->forget('system_email_account_id')->save();
        // }
    }

    /**
    * The relations that are required for the responsee
    *
    * @return array
    */
    protected function eagerLoad()
    {
        $this->withCount([
            'messages as unread_count' => fn ($query) => $query->where('is_read', false),
        ]);

        return [
            'user',
            'folders' => fn ($query) => $query->withCount([
                'messages as unread_count' => fn ($query) => $query->where('is_read', false),
            ]),
            'sentFolder',
            'trashFolder',
            'oAuthAccount',
        ];
    }
}
