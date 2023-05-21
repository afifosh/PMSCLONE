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

namespace Modules\Contacts\Resource\Company;

use Modules\Core\Actions\ForceDeleteAction;
use Modules\Core\Actions\RestoreAction;
use Modules\Core\Table\Table;

class CompanyTable extends Table
{
    /**
     * Indicates whether the user can customize columns orders and visibility
     */
    public bool $customizeable = true;

    /**
     * Additional fields to be selected with the query
     */
    protected function addSelect(): array
    {
        return [
            // The user_id must remains even if the BelongsToColumn::make('owner') is removed
            'user_id', // is for the policy checks,
        ];
    }

    /**
     * Get the actions intended for the trashed table
     *
     * NOTE: No authorization is performed on these action, all actions will be visible to the user
     */
    public function actionsForTrashedTable(): array
    {
        return [new RestoreAction, new ForceDeleteAction];
    }

    /**
     * Boot table
     */
    public function boot(): void
    {
        $this->orderBy('created_at', 'desc');
    }
}
