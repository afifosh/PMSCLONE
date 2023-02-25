<?php

namespace App\DataTables;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NotificationsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->addColumn('message', function ($row) {
                $data = $row->data;

                if (isset($data['location'])) {
                    return view('notifications.datatables.notification-message', compact('row'));
                }
            })
            ->addColumn('data', function ($row) {
                $data = $row->data;

                if (isset($data['location'])) {
                    return view('notifications.datatables.notification-data', compact('data'));
                }
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->rawColumns(['message', 'device', 'city', 'country', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Notification $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Notification $model): QueryBuilder
    {
        $user = auth()->user();
        $current_guard = Auth::getDefaultDriver();
        if ($current_guard == "web") {
            $notifiable_type = User::class;
        } elseif ($current_guard == "admin") {
            $notifiable_type = Admin::class;
        }
        return $model
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $notifiable_type)
            ->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('notifications-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('message')->title(__('Notification Title'))->width(250),
            Column::make('data'),
            Column::make('created_at')->title(__('How Long ago'))->width(250),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Notifications_' . date('YmdHis');
    }
}
