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

use App\Innoclapps\Models\Model;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Macroable;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Criteria\WithTrashedCriteria;

class RecordFinder
{
    use Macroable;

    protected ?LazyCollection $records = null;

    protected int $limit = 500;

    protected array|string $with = [];

    /**
     * Initialize new RecordFinder instance.
     */
    public function __construct(protected AppRepository $repository)
    {
        $this->repository->pushCriteria(WithTrashedCriteria::class);
    }

    /**
     * Match all of the given values
     */
    public function matchAll(array $attributes) : ?Model
    {
        $this->createCollection();

        if (collect($attributes)->filter(fn ($value) => ! $value)->isNotEmpty()) {
            return null;
        }

        foreach ($this->records as $record) {
            $matches = 0;

            foreach ($attributes as $attribute => $value) {
                if (strcasecmp($record->{$attribute}, $value) === 0) {
                    $matches++;
                }
            }

            if ($matches === count($attributes)) {
                return $record;
            }
        }

        return null;
    }

    /**
     * Match any of the given values
     */
    public function match(array $attributes) : ?Model
    {
        $this->createCollection();

        foreach ($this->records as $record) {
            foreach ($attributes as $attribute => $value) {
                if ($value && strcasecmp($record->{$attribute}, $value) === 0) {
                    return $record;
                }
            }
        }

        return null;
    }

    /**
     * Limit the number of rows loaded via the lazy method
     */
    public function limit(int $limit) : static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add the relationships to lazy load
     */
    public function with(array|string $relationships) : static
    {
        $this->with = $relationships;

        return $this;
    }

    /**
     * Remove the trashed criteria
     */
    public function withoutTrashed() : static
    {
        $this->repository->popCriteria(WithTrashedCriteria::class);

        return $this;
    }

    /**
    * Create lazy collection for the finder
    *
    * @return void
    */
    protected function createCollection()
    {
        if (is_null($this->records)) {
            $this->records = $this->repository->with($this->with)->lazy($this->limit)->remember();
        }
    }
}
