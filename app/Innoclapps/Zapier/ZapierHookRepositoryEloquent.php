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

namespace App\Innoclapps\Zapier;

use App\Innoclapps\Models\ZapierHook;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\ZapierHookRepository;

class ZapierHookRepositoryEloquent extends AppRepository implements ZapierHookRepository
{
    /**
     * Resource cached hooks
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $resourceHooks;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return ZapierHook::class;
    }

    /**
     * Get the given resource registered hooks
     *
     * @param string $name
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResourceHooks(string $name)
    {
        if (! isset($this->resourceHooks[$name])) {
            $this->resourceHooks[$name] = $this->with('user')->findWhere(['resource_name' => $name]);
        }

        return $this->resourceHooks[$name];
    }

    /**
     * Get the given resource actions
     *
     * @param string $resourceName
     * @param string $actionName
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResourceActionZaps(string $resourceName, string $actionName)
    {
        return $this->getResourceHooks($resourceName)->where('action', $actionName);
    }

    /**
     * Delete Zapier hook for the given id and user
     *
     * @param int $id
     * @param int $userId
     *
     * @return boolean
     */
    public function deleteForUser($id, $userId) : bool
    {
        return $this->scopeQuery(fn ($query) => $query->where('user_id', $userId))->delete($id);
    }

    /**
     * Delete hook by given URL
     *
     * @param string $url
     *
     * @return boolean
     */
    public function deleteByUrl(string $url) : bool
    {
        $hook = $this->findWhere(['hook' => $url])->first();

        if ($hook) {
            return $this->delete($hook->id);
        }

        return false;
    }
}
