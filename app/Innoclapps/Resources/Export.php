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

use App\Innoclapps\Export\ExportViaFields;
use App\Innoclapps\Repository\AppRepository;

class Export extends ExportViaFields
{
    /**
     * Chunk size
     *
     * @var integer
     */
    public static int $chunkSize = 500;

    /**
     * Create new Export instance.
     *
     * @param \App\Innoclapps\Resources\Resource $resource
     * @param \App\Innoclapps\Repository\AppRepository $repository
     */
    public function __construct(protected Resource $resource, protected AppRepository $repository)
    {
    }

    /**
     * Provides the export data
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        [$with, $withCount] = $this->resource->getEagerloadableRelations($this->fields());

        return $this->repository->withCount($withCount->all())
            ->with($with->all())
            ->lazy(static::$chunkSize);
    }

    /**
    * Provides the resource available fields
    *
    * @return \App\Innoclapps\Fields\FieldsCollection
    */
    public function fields()
    {
        return $this->resource->resolveFields();
    }

    /**
     * The export file name (without extension)
     *
     * @return string
     */
    public function fileName() : string
    {
        return $this->resource->name();
    }
}
