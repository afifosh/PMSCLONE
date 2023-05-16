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

namespace Modules\MailClient\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Core\Contracts\Resources\Tableable;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Resource\Resource;
use Modules\Core\Table\Table;
use Modules\MailClient\Client\FolderType;
use Modules\MailClient\Criteria\EmailAccountMessageCriteria;
use Modules\MailClient\Criteria\EmailAccountMessagesForUserCriteria;
use Modules\MailClient\Http\Resources\EmailAccountMessageResource;

class EmailMessage extends Resource implements Tableable
{
    /**
     * Indicates whether the resource is globally searchable
     */
    public static bool $globallySearchable = true;

    /**
     * The model the resource is related to
     */
    public static string $model = 'Modules\MailClient\Models\EmailAccountMessage';

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria(): ?string
    {
        return EmailAccountMessagesForUserCriteria::class;
    }

    /**
     * Get the eager loadable relations from the given fields
     */
    public static function getEagerLoadable($fields): array
    {
        return [collect(['folders', 'account']), collect([])];
    }

    /**
     * Prepare global search query.
     */
    public function globalSearchQuery(Builder $query = null): Builder
    {
        return parent::globalSearchQuery($query)->with(['folders', 'account']);
    }

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource(): string
    {
        return EmailAccountMessageResource::class;
    }

    /**
     * The resource name
     */
    public static function name(): string
    {
        return 'emails';
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName(): string
    {
        return 'emails';
    }

    /**
     * Create query when the resource is associated for index.
     */
    public function associatedIndexQuery(Model $primary, bool $applyOrder = true): Builder
    {
        $query = parent::associatedIndexQuery($primary, $applyOrder);

        return $query->withResponseRelations()
            ->whereHas('folders.account', function ($query) {
                return $query->whereColumn('folder_id', '!=', 'trash_folder_id');
            });
    }

    /**
     * Provide the resource table class
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function table($query, Request $request): Table
    {
        $criteria = new EmailAccountMessageCriteria(
            $request->integer('account_id'),
            $request->integer('folder_id')
        );

        $tableClass = $this->getTableClassByFolderType($request->folder_type);

        return new $tableClass($query->criteria($criteria), $request);
    }

    /**
     * Provides the resource available actions
     */
    public function actions(ResourceRequest $request): array
    {
        return [
            (new \Modules\MailClient\Actions\EmailAccountMessageMarkAsRead)->withoutConfirmation(),
            (new \Modules\MailClient\Actions\EmailAccountMessageMarkAsUnread)->withoutConfirmation(),
            new \Modules\MailClient\Actions\EmailAccountMessageMove,
            new \Modules\MailClient\Actions\EmailAccountMessageDelete,
        ];
    }

    /**
     * Get the table FQCN by given folder type
     */
    protected function getTableClassByFolderType(?string $type): string
    {
        if ($type === FolderType::OTHER || $type == 'incoming') {
            return IncomingMessageTable::class;
        }

        return OutgoingMessageTable::class;
    }
}
