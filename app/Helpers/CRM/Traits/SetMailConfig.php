<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\DeliverySettingService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class SetMailConfig
{
    use InstanceCreator;

    public function clear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function set()
    {
        $mailSettings = cache()->remember('app-delivery-settings', 84000, function () {
            return resolve(SettingRepository::class)
                ->getDeliverySettingLists([
                    optional(resolve(DeliverySettingService::class)
                        ->getDefaultSettings('default_mail'))->value,
                    'default_mail_email_name'
                ]);
        });
        if ($mailSettings) {
            Config::set('mail.default', $mailSettings['provider']);
            Config::set('mail.from.address', $mailSettings['from_email']);
            Config::set('mail.from.name', $mailSettings['from_name']);

            if ($mailSettings['provider'] == 'smtp') {
                Config::set('mail.mailers.smtp.host', $mailSettings['host']);
                Config::set('mail.mailers.smtp.port', $mailSettings['port']);
                Config::set('mail.mailers.smtp.encryption', $mailSettings['encryption']);
                Config::set('mail.mailers.smtp.username', $mailSettings['username']);
                Config::set('mail.mailers.smtp.password', $mailSettings['password']);

            } elseif ($mailSettings['provider'] == 'mailgun') {

                Config::set('services.mailgun.domain', $mailSettings['domain_name']);
                Config::set('services.mailgun.secret', $mailSettings['api_key']);

            } elseif ($mailSettings['provider'] == 'amazon_ses') {

                Config::set('mail.default', 'ses');
                Config::set('services.ses.key', $mailSettings['access_key_id']);
                Config::set('services.ses.secret', $mailSettings['secret_access_key']);
                Config::set('services.ses.region', $mailSettings['region']);
            }
        }
    }
}
