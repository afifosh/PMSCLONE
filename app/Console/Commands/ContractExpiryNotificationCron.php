<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Contract;
use App\Notifications\Admin\ContractExpiryNotification;
use App\Services\Core\Setting\SettingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ContractExpiryNotificationCron extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'contract-expiry-notification';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Contract expiry notification cron';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $config = (new SettingService())->getFormattedSettings('contract-notifications');

    if (isset($config['enable_notifications']) && $config['enable_notifications'] == 1) {

      $this->saveLog('Sending contract expiry notifications...');
      $adminsToNotify = Admin::whereIn('id', explode(',', $config['emails']))->get();

      Contract::where('status', 'Active')
        ->where('end_date', '<=', now()->add($config['cycle_unit_value'] * $config['cycle_count'], $config['cycle_unit_name']))
        ->whereDoesntHave('lastExpiryNotification', function ($query) use ($config) {
          $query->where('created_at', '>=', now()->sub($config['cycle_unit_value'], $config['cycle_unit_name']));
        })->with('notifiableUsers')
        ->chunkById(1, function ($contracts) use ($adminsToNotify) {
          foreach ($contracts as $contract) {
            $this->saveLog('Sending notification for contract ' . $contract->subject . ' to ' . ($adminsToNotify->count() + $contract->notifiableUsers->whereNotIn('id', $adminsToNotify->pluck('id'))->count()) . ' admins');
            foreach ($adminsToNotify as $admin) {
              $admin->notify(new ContractExpiryNotification($contract));
              $contract->lastExpiryNotification()->create([
                'sent_to' => $admin->id
              ]);
            }

            foreach ($contract->notifiableUsers as $user) {
              if($adminsToNotify->contains($user))
                continue;
              $user->notify(new ContractExpiryNotification($contract));
              $contract->lastExpiryNotification()->create([
                'sent_to' => $user->id
              ]);
            }
          }
        }, $column = 'id');

      $this->saveLog('Contract expiry notifications sent.');
    } else {
      $this->saveLog('Contract expiry notifications are disabled.');
    }
  }

  protected function saveLog($message)
  {
    $this->info($message);

    Log::channel('contract-expiry-reminders')->info($message);
  }
}
