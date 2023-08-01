<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Resource;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filters\DateTime as DateTimeFilter;
use Modules\Core\Filters\Radio as RadioFilter;
use Modules\Core\Filters\Text as TextFilter;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Table\Column;
use Modules\Core\Table\DateTimeColumn;
use Modules\Core\Table\HasOneColumn;
use Modules\Core\Table\Table;

class IncomingMessageTable extends Table
{
    /**
     * Additional database columns to select for the table query.
     */
    protected array $select = [
        'is_read',
        'email_account_id', // uri key for json resource
    ];

    /**
     * Provide the table available default columns.
     */
    public function columns(): array
    {
        return [
            Column::make('subject', __('mailclient::inbox.subject')),

            HasOneColumn::make('from', 'address', __('mailclient::inbox.from'))
                ->select('name'),

            DateTimeColumn::make('date', __('mailclient::inbox.date')),
        ];
    }

    /**
     * Get the resource available Filters
     */
    public function filters(ResourceRequest $request): array
    {
        return [
            TextFilter::make('subject', __('mailclient::inbox.subject')),

            TextFilter::make('to', __('mailclient::inbox.to'))->withoutNullOperators()
                ->query(function ($builder, $value, $condition, $sqlOperator) {
                    return $builder->whereHas(
                        'from',
                        fn (Builder $query) => $query->where(
                            'address',
                            $sqlOperator['operator'],
                            $value,
                            $condition
                        )->orWhere(
                            'name',
                            $sqlOperator['operator'],
                            $value,
                            $condition
                        )
                    );
                }),

            TextFilter::make('from', __('mailclient::inbox.from'))->withoutNullOperators()
                ->query(function ($builder, $value, $condition, $sqlOperator) {
                    return $builder->whereHas(
                        'to',
                        fn (Builder $query) => $query->where(
                            'address',
                            $sqlOperator['operator'],
                            $value,
                            $condition
                        )->orWhere(
                            'name',
                            $sqlOperator['operator'],
                            $value,
                            $condition
                        )
                    );
                }),

            DateTimeFilter::make('date', __('mailclient::inbox.date')),

            RadioFilter::make('is_read', __('mailclient::inbox.filters.is_read'))->options([
                true => __('core::app.yes'),
                false => __('core::app.no'),
            ]),
        ];
    }

    /**
     * Boot table
     */
    public function boot(): void
    {
        // Eager load the folders as the folders are used to create the path
        $this->orderBy('date', 'desc')->with('folders');
    }
}
