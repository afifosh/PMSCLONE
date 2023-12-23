<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->createModulesPermissions();
  }
  public function createModulesPermissions()
  {
    $rawData = $this->modulesPermissions();
    foreach ($rawData as $guard => $modules) {
      foreach ($modules as $module) {
        $module_id = Module::insertGetId(['name' => $module['module']]);
        DB::table('permissions')->insert(collect($module['permissions'])->map(fn ($permission) => [
          'module_id' => $module_id,
          'name' => $permission,
          'guard_name' => $guard
        ])->toArray());
      }
    }
  }
  public function modulesPermissions()
  {
    return [
      'admin' => [
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user', 'impersonate user']],
        ['module' => 'Roles Management', 'permissions' => ['read role', 'create role', 'update role', 'delete role']],
        ['module' => 'Company Roles', 'permissions' => ['read company role', 'create company role', 'update company role', 'delete company role']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
        ['module' => 'Programs ACL Rule', 'permissions' => ['read acl', 'create acl', 'update acl', 'delete acl']],
        ['module' => 'Programs', 'permissions' => ['read program', 'create program', 'update program', 'delete program']],
        // contracts related permissions
        ['module' => 'Contracts', 'permissions' => ['read contract', 'create contract', 'update contract', 'delete contract', 'review contract']],
        ['module' => 'Contract Invoices Retention', 'permissions' => ['release contract invoices retention']],
        ['module' => 'Contract Parties', 'permissions' => ['read contract party', 'create contract party', 'update contract party', 'delete contract party']],
        ['module' => 'Contract Stages', 'permissions' => ['read contract stage', 'create contract stage', 'update contract stage', 'delete contract stage']],
        ['module' => 'Contract Phases', 'permissions' => ['read contract phase', 'create contract phase', 'update contract phase', 'delete contract phase', 'review contract phase']],
        ['module' => 'Contract Phase Taxes', 'permissions' => ['read phase tax', 'create phase tax', 'update phase tax', 'delete phase tax']],
        ['module' => 'Contract Phase Deductions', 'permissions' => ['read phase deduction', 'create phase deduction', 'update phase deduction', 'delete phase deduction']],
        ['module' => 'Contract Phase Manual Subtotal', 'permissions' => ['manage phase manual subtotal']],
        ['module' => 'Contract Phase Manual Total', 'permissions' => ['manage phase manual total']],
        ['module' => 'Contract Phase Activities', 'permissions' => ['read phase activity']],
        ['module' => 'Contract Phase Comments', 'permissions' => ['read phase comment', 'create phase comment', 'update phase comment', 'delete phase comment', 'react phase comment']],
        ['module' => 'Contract Documents', 'permissions' => ['read contract document', 'create contract document', 'update contract document', 'delete contract document']],
        ['module' => 'Contract Document Signatures', 'permissions' => ['read contract document signature', 'create contract document signature', 'update contract document signature', 'delete contract document signature']],
        ['module' => 'Contract Document Stamps', 'permissions' => ['read contract document stamp', 'create contract document stamp', 'update contract document stamp', 'delete contract document stamp']],
        ['module' => 'Contract Document Comments', 'permissions' => ['read contract document comment', 'create contract document comment', 'update contract document comment', 'delete contract document comment', 'react contract document comment']],
        ['module' => 'Contract Document Stats', 'permissions' => ['read contract doc stats']],
        ['module' => 'Contract Events', 'permissions' => ['read contract event']],
        ['module' => 'Contract Logs', 'permissions' => ['read contract log']],
        ['module' => 'Contract Change Requests', 'permissions' => ['read change request', 'create change request', 'update change request', 'delete change request', 'approve change request', 'reject change request']],
        ['module' => 'Contract Notifiable Users', 'permissions' => ['read contract noti-users', 'create contract noti-users', 'update contract noti-users', 'delete contract noti-users']],
        ['module' => 'Contract Settings', 'permissions' => ['pause contract', 'resume contract', 'terminate contract']],
        ['module' => 'Contract Review Tracking', 'permissions' => ['read contract rtracking']],
        ['module' => 'Contract Payment Plan Review Tracking', 'permissions' => ['read contract payplan rtracking']],

        // invoices
        ['module' => 'Invoices', 'permissions' => ['read invoice', 'create invoice', 'update invoice', 'delete invoice', 'void invoice', 'merge invoice']],
        // invoice items
        ['module' => 'Invoice Items', 'permissions' => ['read invoice item', 'create invoice item', 'update invoice item', 'delete invoice item']],
        ['module' => 'Invoice Item Taxes', 'permissions' => ['read invoice item tax', 'create invoice item tax', 'update invoice item tax', 'delete invoice item tax']],
        ['module' => 'Invoice Item Deduction', 'permissions' => ['read invoice item deduction', 'create invoice item deduction', 'update invoice item deduction', 'delete invoice item deduction']],
        ['module' => 'Invoice Item Manual Subtotal', 'permissions' => ['manage invoice item manual subtotal']],
        ['module' => 'Invoice Item Manual Total', 'permissions' => ['manage invoice item manual total']],
        // invoice documents
        ['module' => 'Invoice Documents', 'permissions' => ['read invoice document', 'create invoice document', 'update invoice document', 'delete invoice document']],
        ['module' => 'Invoice Document Signatures', 'permissions' => ['read invoice document signature', 'create invoice document signature', 'update invoice document signature', 'delete invoice document signature']],
        ['module' => 'Invoice Document Stamps', 'permissions' => ['read invoice document stamp', 'create invoice document stamp', 'update invoice document stamp', 'delete invoice document stamp']],
        ['module' => 'Invoice Document Comments', 'permissions' => ['read invoice document comment', 'create invoice document comment', 'update invoice document comment', 'delete invoice document comment', 'react invoice document comment']],
        ['module' => 'Invoice Comments', 'permissions' => ['read invoice comment', 'create invoice comment', 'update invoice comment', 'delete invoice comment', 'react invoice comment']],
        ['module' => 'Invoice Tax Report', 'permissions' => ['read invoice tax report']],
        ['module' => 'Tax Authority Invoice', 'permissions' => ['read tax authority invoice', 'delete tax authority invoice']],
        ['module' => 'Tax Authority Invoice Comments', 'permissions' => ['read ta-invoice comment', 'create ta-invoice comment', 'update ta-invoice comment', 'delete ta-invoice comment', 'react ta-invoice comment']],

        ['module' => 'Invoice Stats', 'permissions' => ['read invoice stats']],

        // payments
        ['module' => 'Payments', 'permissions' => ['read payment', 'create payment', 'update payment', 'delete payment']],

        // contract controls
        ['module' => 'Contract Types', 'permissions' => ['read contract type', 'create contract type', 'update contract type', 'delete contract type']],
        ['module' => 'Contract Categories', 'permissions' => ['read contract category', 'create contract category', 'update contract category', 'delete contract category']],
        ['module' => 'Contract Document Controls', 'permissions' => ['read contract doc-control', 'create contract doc-control', 'update contract doc-control']],
        // invoice controls
        ['module' => 'Invoice Document Controls', 'permissions' => ['read invoice doc-control', 'create invoice doc-control', 'update invoice doc-control']], // requesting docs
        ['module' => 'Financial Years', 'permissions' => ['read financial year', 'create financial year', 'update financial year', 'delete financial year']],
        ['module' => 'Program Accounts', 'permissions' => ['read program account', 'create program account', 'update program account', 'delete program account', 'deposit in pa', 'transfer in pa']],
        ['module' => 'Taxes', 'permissions' => ['read tax', 'create tax', 'update tax', 'delete tax']],
        ['module' => 'Retentions', 'permissions' => ['read retention', 'create retention', 'update retention', 'delete retention']],
        ['module' => 'Down payments', 'permissions' => ['read downpayment', 'create downpayment', 'update downpayment', 'delete downpayment']],

        // forms
        ['module' => 'Forms', 'permissions' => ['read form', 'create form', 'update form', 'delete form', 'export form', 'design form', 'duplicate form']],
        ['module' => 'Form Rules', 'permissions' => ['read form rule', 'create form rule', 'update form rule', 'delete form rule']],
        ['module' => 'Form Templates', 'permissions' => ['read form template', 'create form template', 'update form template', 'delete form template', 'export form template']],
        ['module' => 'Form Submissions', 'permissions' => ['read submitted form', 'update submitted form', 'delete submitted form', 'export submitted form']],

      ],
      'web' => [
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user']],
        ['module' => 'Roles', 'permissions' => ['read role']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
      ],
    ];
  }
}
