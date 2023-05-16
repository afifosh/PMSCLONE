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

/** @mixin \Modules\Core\Models\Filter */
class FilterResource extends JsonResource
{
    use ProvidesCommonData;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->withCommonData([
            'name' => $this->name,
            'identifier' => $this->identifier,
            'rules' => $this->rules,
            'user_id' => $this->user_id,
            'is_shared' => $this->is_shared,
            'is_system_default' => $this->is_system_default,
            'is_readonly' => $this->is_readonly,
            'defaults' => $this->defaults->map(function ($default) {
                return [
                    'user_id' => $default->user_id,
                    'view' => $default->view,
                ];
            })->values(),
        ], $request);
    }
}
