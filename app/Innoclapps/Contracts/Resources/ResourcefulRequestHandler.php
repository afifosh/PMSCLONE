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

namespace App\Innoclapps\Contracts\Resources;

interface ResourcefulRequestHandler
{
    /**
     * Handle the resource index action
     *
     * @return mixed
     */
    public function index();

    /**
     * Handle the resource store action
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store();

    /**
     * Handle the resource show action
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id);

    /**
     * Handle the resource update action
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id);

    /**
     * Handle the resource destroy action
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id);

    /**
     * Handle the resource force delete action
     *
     * @param int $id
     *
     * @return mixed
     */
    public function forceDelete($id);

    /**
     * Handle the resource restore action
     *
     * @param int $id
     *
     * @return mixed
     */
    public function restore($id);
}
