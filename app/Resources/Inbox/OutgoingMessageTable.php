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

namespace App\Resources\Inbox;

use App\Innoclapps\Table\Column;
use App\Innoclapps\Table\HasManyColumn;
use App\Innoclapps\Table\DateTimeColumn;

class OutgoingMessageTable extends IncomingMessageTable
{
    /**
    * Provides table available default columns
    *
    * @return array
    */
    public function columns() : array
    {
        return [
            Column::make('subject', __('inbox.subject')),

            HasManyColumn::make('to', 'address', __('inbox.to'))
                ->select('name'),

            DateTimeColumn::make('date', __('inbox.date')),
        ];
    }
}
