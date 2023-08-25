<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\Contract\ContractSettingController;
use App\Models\Contract;
use Illuminate\Console\Command;

class ContractTerminationCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'contract-termination-cron';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Terminate contract if scheduled for termination';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $settingsClass = new ContractSettingController();
    Contract::where('status', '!=','Terminated')->whereHas('events', function($q){
      $q->where('event_type', 'Terminated')->where('modifications->termination_date', '<=', now());
    })->chunk(100, function($contracts) use ($settingsClass){
      foreach($contracts as $contract){
        $settingsClass->sendTerminateNotification($contract);
        $contract->update(['status' => 'Terminated']);
      }
    });
  }
}
