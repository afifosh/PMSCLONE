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

namespace App\Http\Resources;

use Illuminate\Support\Collection;

trait ProvidesCommonData
{
    /**
     * Add common data to the resource.
     *
     * @param  \Modules\Core\Resource\Http\ResourceRequest|\Illuminate\Http\Request  $request
     */
    protected function withCommonData(array $data, $request): array
    {
        $data = parent::withCommonData($data, $request);

        if ($this->shouldMergeAssociations()) {
            $data[] = $this->merge([
                'associations' => $this->prepareAssociationsForResponse(),
            ]);
        }

        return $data;
    }

    /**
     * Get the resource associations
     */
    protected function prepareAssociationsForResponse(): Collection
    {
        return collect($this->resource->associatedResources())
            ->map(function ($resourceRecords) {
                return $resourceRecords->map(function ($record) {
                    // Only included needed data for the front-end
                    // if needed via API, users can use the ?with= parameter to load associated resources
                    return [
                        'id' => $record->id,
                        'display_name' => $record->display_name,
                        'path' => $record->path,
                    ];
                });
            });
    }

    /**
     * Check whether a resource has associations and should be merged
     *
     * Associations are merged only if they are previously eager loaded
     */
    protected function shouldMergeAssociations(): bool
    {
        if (! method_exists($this->resource, 'associatedResources')) {
            return false;
        }

        return $this->resource->associationsLoaded();
    }
}
