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

namespace App\Innoclapps\Resources;

use App\Innoclapps\Criteria\RequestCriteria;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Criteria\OnlyTrashedCriteria;
use App\Innoclapps\Criteria\WithTrashedCriteria;
use App\Innoclapps\Resources\Http\ResourcefulRequest;
use App\Innoclapps\Contracts\Resources\ResourcefulRequestHandler;

class ResourcefulHandler implements ResourcefulRequestHandler
{
    use AssociatesResources;

    /**
     * Indicates whether the trashed records should be queried
     *
     * @var boolean
     */
    protected bool $withTrashed = false;

    /**
     * Indicates whether the only trashed records should be queried
     *
     * @var boolean
     */
    protected bool $onlyTrashed = false;

    /**
     * Initialize the resourceful handeler
     *
     * @param \App\Innoclapps\Resources\Http\ResourcefulRequest $request
     * @param \App\Innoclapps\Repository\AppRepository $repository
     */
    public function __construct(protected ResourcefulRequest $request, protected AppRepository $repository)
    {
    }

    /**
     * Handle the resource index action
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        $repository = $this->resource()->indexQuery(
            $this->resource()->applyOrder($this->repository)
        );

        return $this->withSoftDeleteCriteria($repository)
            ->pushCriteria(RequestCriteria::class)
            ->paginate($this->getPerPage());
    }

    /**
     * Handle the resource store action
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store()
    {
        return $this->handleAssociatedResources(
            $this->repository->create(
                $this->request->all()
            )
        );
    }

    /**
     * Handle the resource show action
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function show($id)
    {
        return $this->resource()->displayQuery(
            $this->withSoftDeleteCriteria($this->repository)
        )->find($id);
    }

    /**
     * Handle the resource update action
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id)
    {
        return $this->handleAssociatedResources(
            $this->withSoftDeleteCriteria($this->repository)->update(
                $this->request->all(),
                $id
            )
        );
    }

    /**
     * Handle the resource destroy action
     *
     * @param int $id
     *
     * @return string
     */
    public function destroy($id)
    {
        $this->withSoftDeleteCriteria($this->repository)->delete($id);

        return '';
    }

    /**
     * Force delete the resource record
     *
     * @param int $id
     *
     * @return string
     */
    public function forceDelete($id)
    {
        $this->withSoftDeleteCriteria($this->repository)->forceDelete($id);

        return '';
    }

    /**
     * Restore the soft deleted resource record
     *
     * @param int $id
     *
     * @return string
     */
    public function restore($id)
    {
        $this->withSoftDeleteCriteria($this->repository)->restore($id);

        return '';
    }

    /**
     * Sync the given record associations
     *
     * @param \Illuminate\Database\Eloquent\Model $record
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function handleAssociatedResources($record)
    {
        if ($this->resource()->isAssociateable()) {
            $this->syncAssociations(
                $this->resource(),
                $record->getKey(),
                $this->filterAssociations(
                    $this->resource(),
                    $this->request->associateables()
                )
            );
        }

        return $record;
    }

    /**
     * Query the resource with trashed record
     *
     * @return static
     */
    public function withTrashed()
    {
        $this->withTrashed = true;

        return $this;
    }

    /**
     * Query the resource with only trashed record
     *
     * @return static
     */
    public function onlyTrashed()
    {
        $this->onlyTrashed = true;

        return $this;
    }

    /**
     * Apply the soft deletes criteria to the given repository
     *
     * @param \App\Innoclapps\Repository\AppRepository $repository
     *
     * @return \App\Innoclapps\Repository\AppRepository
     */
    protected function withSoftDeleteCriteria($repository)
    {
        if ($this->withTrashed) {
            $repository->pushCriteria(WithTrashedCriteria::class);
        } elseif ($this->onlyTrashed) {
            $repository->pushCriteria(OnlyTrashedCriteria::class);
        }

        return $repository;
    }

    /**
     * Get the resource from the request
     *
     * @return \App\Innoclapps\Resources\Resource
     */
    protected function resource()
    {
        return $this->request->resource();
    }

    /**
     * Get the number of models to return per page.
     *
     * @return null|int
     */
    protected function getPerPage()
    {
        return $this->request->input('per_page');
    }
}
