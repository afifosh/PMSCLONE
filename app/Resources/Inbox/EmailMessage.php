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

namespace App\Resources\Inbox;

use Illuminate\Http\Request;
use App\Innoclapps\Table\Table;
use App\Innoclapps\Resources\Resource;
use App\Innoclapps\MailClient\FolderType;
use App\Innoclapps\Contracts\Resources\Tableable;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Http\Resources\EmailAccountMessageResource;
use App\Criteria\EmailAccount\EmailAccountMessageCriteria;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Criteria\EmailAccountMessage\EmailAccountMessagesForUserCriteria;

class EmailMessage extends Resource implements Tableable
{
    /**
     * Indicates whether the resource is globally searchable
     */
    public static bool $globallySearchable = true;

    /**
     * The model the resource is related to
     */
    public static string $model = 'App\Models\EmailAccountMessage';

    /**
     * Get the underlying resource repository
     *
     * @return \App\Innoclapps\Repository\AppRepository
     */
    public static function repository()
    {
        return resolve(EmailAccountMessageRepository::class);
    }

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria() : ?string
    {
        return EmailAccountMessagesForUserCriteria::class;
    }

    /**
     * Get the eager loadable relations from the given fields
     */
    public static function getEagerloadableRelations($fields) : array
    {
        return [collect(['folders', 'account']), collect([])];
    }

    /**
     * Prepare global search query
     *
     * @param null|\App\Innoclapps\Repositories\AppRepository $repository
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function globalSearchQuery(int $limit, $repository = null)
    {
        return parent::globalSearchQuery($limit, $repository)->with(['folders', 'account']);
    }

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource() : string
    {
        return EmailAccountMessageResource::class;
    }

    /**
     * The resource name
     */
    public static function name() : string
    {
        return 'emails';
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName() : string
    {
        return 'emails';
    }

    /**
     * Create query when the resource is associated for index
     *
     * @param \App\Innoclapps\Models\Model $primary
     * @param bool $applyOrder
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function associatedIndexQuery($primary, $applyOrder = true)
    {
        return tap(parent::associatedIndexQuery($primary, $applyOrder), function ($repository) {
            $repository->withResponseRelations()
                ->whereHas('folders.account', function ($query) {
                    return $query->whereColumn('folder_id', '!=', 'trash_folder_id');
                });
        });
    }

    /**
     * Provide the resource table class
     *
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     */
    public function table($repository, Request $request) : Table
    {
        $criteria = new EmailAccountMessageCriteria(
            $request->integer('account_id'),
            $request->integer('folder_id')
        );

        $tableClass = $this->getTableClassByFolderType($request->folder_type);

        return new $tableClass($repository->pushCriteria($criteria), $request);
    }

    /**
     * Provides the resource available actions
     */
    public function actions(ResourceRequest $request) : array
    {
        return [
            (new Actions\EmailAccountMessageMarkAsRead)->withoutConfirmation(),
            (new Actions\EmailAccountMessageMarkAsUnread)->withoutConfirmation(),
            new Actions\EmailAccountMessageMove,
            new Actions\EmailAccountMessageDelete,
        ];
    }

    /**
     * Get the table FQCN by given folder type
     */
    protected function getTableClassByFolderType(?string $type) : string
    {
        if ($type === FolderType::OTHER || $type == 'incoming') {
            return IncomingMessageTable::class;
        }

        return OutgoingMessageTable::class;
    }
}
