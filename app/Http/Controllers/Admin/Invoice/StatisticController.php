<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class StatisticController extends Controller
{
  public function __invoke()
  {
    // top 5 companies by number of invoices
    $data['companiesByInvoices'] = $this->getCompaniesByInvoices();


    // top 5 invoices by value
    $data['invoicesByValue'] = $this->getInvoicesByValue();

    // invoices count expiring in 30,60,90 days
    $data['invoicesByDueDate'] = $this->getInvoicesByDueDate();

    // invoices by status
    $data['invoicesByStatus'] = $this->getInvoicesByStatus();

    $data['invoicesByDistribution'] = $this->getInvoicesByAssignees();

    // list of invoices expiring in 30, 60, 90 days
    $data['expiringInvoicesList'] = Invoice::whereNotIn('status', ['Paid', 'Cancelled'])
      ->whereNotNull('due_date')
      ->where('due_date', '>', now())
      ->where('due_date', '<', now()->addDays(90))
      ->get();
    return view('admin.pages.invoices.statistics.index', $data);
  }

  protected function getInvoicesByAssignees()
  {
    // select invoices group by assignable_type
    $data = Invoice::leftjoin('contracts', 'contracts.id', '=', 'invoices.contract_id')
      // contracts.assignable_type ='App\Models\Company' && contracts.assignable_id = companies.id
      ->leftjoin('companies', function($join) {
        $join->on('contracts.assignable_id', '=', 'companies.id')
          ->where('contracts.assignable_type', '=', 'App\Models\Company');
      })
      // select invoices count group by companies.type
      ->selectRaw('companies.type, COUNT(*) as invoice_count')
      ->groupBy('companies.type')
      ->get();

    $data = $data->toArray();
    foreach ($data as $key => $value) {
      $data[$key]['id'] = $key + 1;
    }
    return $data;
  }

  protected function getInvoicesByStatus()
  {
    $invoices = Invoice::selectRaw('count(*) as Total')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as Draft')
      ->selectRaw('count(case when deleted_at is null and status = "Sent" then 1 end) as Sent')
      ->selectRaw('count(case when deleted_at is null and status = "Paid" then 1 end) as Paid')
      ->selectRaw('count(case when deleted_at is null and status = "PartialPaid" then 1 end) as PartialPaid')
      ->selectRaw('count(case when deleted_at is null and status = "Cancelled" then 1 end) as Cancelled')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as Trashed')
      ->withTrashed()
      ->first();

    $invoices = $invoices->toArray();
    $invoices['total'] = $invoices['Total'];
    unset($invoices['Total']);

    return $invoices;
  }

  protected function getInvoicesByValue()
  {
    $data = Invoice::selectRaw('id, CONCAT("INV-", LPAD(id, 4, "0")) as subject , (invoices.total / 1000) as amount')
      ->whereNull('invoices.deleted_at')
      ->orderBy('invoices.total', 'desc')
      ->limit(5)
      ->get();

    $data = array_merge($data->toArray(), array_fill(0, 5 - count($data), ['id' => '', 'subject' => '', 'value' => 0,]));

    return $data;
  }

  protected function getCompaniesByInvoices()
  {
    $data = Company::select('companies.id', 'companies.name')
    ->leftJoin('contracts', function($q){
      $q->on('contracts.assignable_id', '=', 'companies.id')
        ->where('contracts.assignable_type', '=', 'App\Models\Company');
    })
    ->leftJoin('invoices', 'contracts.id', '=', 'invoices.contract_id')
    ->selectRaw('companies.name, COUNT(invoices.id) as invoice_count')
    ->groupBy('companies.id')
    ->orderByDesc('invoice_count')
    ->limit(5)
    ->get();

    // calculate percentage
    $total = $data->sum('invoice_count');
    $data = $data->toArray();
    foreach ($data as $key => $value) {
      $data[$key]['percentage'] = round($value['invoice_count'] / $total * 100, 2);
      unset($data[$key]['avatar']);
      unset($data[$key]['detail']);
    }

    // data must have 5 indexs
    $data = array_merge($data, array_fill(0, 5 - count($data), ['name' => '', 'total' => 0, 'percentage' => 0]));

    return $data;
  }

  protected function getInvoicesByDueDate()
  {
    $data = Invoice::select(
      DB::raw('CASE
          WHEN DATEDIFF(due_date, now()) <= 30 THEN "30_days"
          WHEN DATEDIFF(due_date, now()) > 30 AND DATEDIFF(due_date, now()) <= 60 THEN "60_days"
          WHEN DATEDIFF(due_date, now()) > 60 AND DATEDIFF(due_date, now()) <= 90 THEN "90_days"
      END as time_period'),
      DB::raw('COUNT(*) as invoices_count')
    )
      ->whereNotIn('status', ['Paid', 'Cancelled'])
      ->where('due_date', '>', now())
      ->where('due_date', '<=', now()->addDays(90))
      ->groupBy('time_period')
      ->get();

    $data = $data->whereNotNull('time_period');

    foreach ($data as $key => $value) {
      $data[$key]['time_period'] = str_replace('_', ' ', $value['time_period']);
    }
    // set default values for time periods with time period name and count 0
    $timePeriods = ['30 days', '60 days', '90 days'];
    foreach ($timePeriods as $timePeriod) {
      if (!isset($data->where('time_period', $timePeriod)->first()->time_period)) {
        $data[] = ['time_period' => $timePeriod, 'invoices_count' => 0];
      }
    }

    // sort by timePeriods array
    $data = $data->sortBy(function ($model) use ($timePeriods) {
      return array_search($model['time_period'], $timePeriods);
    });

    $data = $data->values()->all();
    return $data;
  }
}
