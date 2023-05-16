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

namespace Modules\Core\Http\Resources;

use App\Http\Resources\ProvidesCommonData;
use Illuminate\Http\Request;
use Modules\Core\JsonResource;

/** @mixin \Modules\Core\Models\Media */
class MediaResource extends JsonResource
{
    use ProvidesCommonData;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->withCommonData([
            'file_name' => $this->basename,
            'extension' => $this->extension,
            'size' => $this->size,
            'disk_path' => $this->getDiskPath(),
            'mime_type' => $this->mime_type,
            'aggregate_type' => $this->aggregate_type,
            'view_url' => $this->getViewUrl(),
            'preview_url' => $this->getPreviewUrl(),
            'preview_path' => $this->previewPath(),
            'download_url' => $this->getDownloadUrl(),
            'download_path' => $this->downloadPath(),
            'pending_data' => $this->whenLoaded('pendingData'),
        ], $request);
    }
}
