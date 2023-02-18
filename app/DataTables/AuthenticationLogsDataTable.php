<?php

namespace App\DataTables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuthenticationLogsDataTable extends DataTable
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
            
            ->addColumn('ip_address', function ($row) {
                return $row->ip_address;
            })
            ->addColumn('user_agent', function ($row) {
                return $row->user_agent;
            })
            ->addColumn('login_at', function ($row) {
                return $row->login_at->diffForHumans();
            })
            ->addColumn('login_successful', function ($row) {
                return view('admin.pages.account.login-status', compact('row'));
            })
            ->addColumn('location', function ($row) {
                return view('admin.pages.account.auth-location', compact('row'));
            })

            ->rawColumns(['ip_address', 'user_agent', 'login_at', 'login_successful', 'location']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AuthenticationLog $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AuthenticationLog $model): QueryBuilder
    {
        return $model->newQuery();

        $user = auth()->user();
        $current_guard = Auth::getDefaultDriver();
        if ($current_guard == "web") {
            $notifiable_type = User::class;
        } elseif ($current_guard == "admin") {
            $notifiable_type = Admin::class;
        }
        return $model
            ->where('authenticatable_id', $user->id)
            ->where('authenticatable_type', $notifiable_type)
            ->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('authenticationlogs-table')
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
            Column::make('ip_address'),
            Column::make('user_agent'),
            Column::make('login_at'),
            Column::make('login_successful'),
            Column::make('location'),
            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'AuthenticationLogs_' . date('YmdHis');
    }
}
