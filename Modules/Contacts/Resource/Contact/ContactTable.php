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

namespace Modules\Contacts\Resource\Contact;

use Modules\Core\Actions\ForceDeleteAction;
use Modules\Core\Actions\RestoreAction;
use Modules\Core\Criteria\SearchByFirstNameAndLastNameCriteria;
use Modules\Core\Criteria\TableRequestCriteria;
use Modules\Core\Table\Table;

class ContactTable extends Table
{
    /**
     * Indicates whether the user can customize columns orders and visibility
     */
    public bool $customizeable = true;

    /**
     * Additional fields to be selected with the query
     */
    public function addSelect(): array
    {
        return [
            'user_id',     // user_id is for the policy checks
            'avatar',      // avatar is for the first column avatar
            'first_name',  // For concat display_name
            'last_name',   // For concat display_name
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
     * Provide the attributes that should be appended within the response
     */
    protected function appends(): array
    {
        return ['avatar_url'];
    }

    /**
     * Create new TableRequestCriteria criteria instance
     */
    protected function createTableRequestCriteria(): TableRequestCriteria
    {
        return (new TableRequestCriteria(
            $this->getUserColumns(),
            $this
        ))->appends(fn ($query) => $query->orWhere(function ($query) {
            $query->criteria(SearchByFirstNameAndLastNameCriteria::class);
        }));
    }

    /**
     * Boot table
     */
    public function boot(): void
    {
        $this->orderBy('created_at', 'desc');
    }
}
