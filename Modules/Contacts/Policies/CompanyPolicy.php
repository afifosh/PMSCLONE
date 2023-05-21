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

namespace Modules\Contacts\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Contacts\Models\Company;
use Modules\Users\Models\User;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        if ($user->can('view all companies')) {
            return true;
        }

        if ((int) $company->user_id === (int) $user->id) {
            return true;
        }

        if ($company->user_id && $user->can('view team companies')) {
            return $user->managesAnyTeamsOf($company->user_id);
        }

        return false;
    }

    /**
     * Determine if the given user can create companies.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        if ($user->can('edit all companies')) {
            return true;
        }

        if ($user->can('edit own companies')) {
            return (int) $user->id === (int) $company->user_id;
        }

        if ($company->user_id && $user->can('edit team companies')) {
            return $user->managesAnyTeamsOf($company->user_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        if ($user->can('delete any company')) {
            return true;
        }

        if ($user->can('delete own companies')) {
            return (int) $user->id === (int) $company->user_id;
        }

        if ($company->user_id && $user->can('delete team companies')) {
            return $user->managesAnyTeamsOf($company->user_id);
        }

        return false;
    }

    /**
     * Determine whether the user can export companies.
     */
    public function export(User $user): bool
    {
        return $user->can('export companies');
    }
}
