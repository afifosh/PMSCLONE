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

namespace App\Innoclapps\Repository;

use Illuminate\Support\Collection;
use App\Innoclapps\Criteria\RequestCriteria;
use App\Innoclapps\Contracts\Repository\CriteriaInterface;
use App\Innoclapps\Repository\Exceptions\RepositoryException;

trait HandlesCriteria
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * The criterias that should be appended to the RequestCriteria
     *
     * @var array
     */
    protected $appendToRequestCriteria = [];

    /**
    * Get Collection of Criteria
    *
    * @return \Illuminate\Support\Collection
    */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Find data by criteria
     *
     * @param \App\Innoclapps\Contracts\Repository\CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        $results     = $this->model->get();
        $this->resetModel();

        return $this->parseResult($results);
    }

    /**
     * Skip criteria
     *
     * @param bool $status
     *
     * @return static
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return static
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Append another crteria to the request criteria
     * Useful when we need additional OR in the request criteria query
     *
     * For example, if you have a criteria that uses orWhere and we just push the criteria to the repository
     * It may produce unwanted results when we have another AND criteria e.q. WHERE first_criteria=value OR or_criteria=value
     *
     * @param \App\Innoclapps\Contracts\Repository\CriteriaInterface $criteria
     *
     * @return static
     */
    public function appendToRequestCriteria(CriteriaInterface $criteria)
    {
        $this->appendToRequestCriteria[] = $criteria;

        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param mixed $criteria
     *
     * @return static
     */
    public function popCriteria($criteria)
    {
        if (is_array($criteria)) {
            foreach ($criteria as $class) {
                $this->popCriteria($class);
            }
        } else {
            $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
                if (is_object($item) && is_string($criteria)) {
                    return $item::class === $criteria;
                }

                if (is_string($item) && is_object($criteria)) {
                    return $item === $criteria::class;
                }

                return $item::class === $criteria::class;
            });
        }

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return static
     *
     * @throws \App\Innoclapps\Repository\Exceptions\RepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = app($criteria);
        }

        if (! $criteria instanceof CriteriaInterface) {
            throw new RepositoryException(
                'Class ' . $criteria::class . ' must be an instance of ' . CriteriaInterface::class
            );
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Apply criteria in current Query
     *
     * @return static
     */
    protected function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $this->getCriteria()->whereInstanceOf(CriteriaInterface::class)
            ->each(function ($criteria) {
                if ($criteria instanceof RequestCriteria) {
                    foreach ($this->appendToRequestCriteria as $appendCriteria) {
                        $criteria->appends($appendCriteria);
                    }
                }

                $this->model = $criteria->apply($this->model, $this);
            });

        return $this;
    }
}
