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

namespace App\Innoclapps\Models;

use App\Innoclapps\Concerns\HasCreator;

class Workflow extends Model
{
    use HasCreator;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'title', 'description', 'trigger_type', 'action_type', 'data', 'created_by', 'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data'             => 'array',
        'is_active'        => 'boolean',
        'total_executions' => 'int',
        'created_by'       => 'int',
    ];
}
