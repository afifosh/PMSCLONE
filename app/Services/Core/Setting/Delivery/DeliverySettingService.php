<?php


namespace App\Services\Core\Setting\Delivery;

use App\Repositories\SettingRepository;
use App\Services\Core\BaseService;

class DeliverySettingService extends BaseService
{

    public function update($key, $value, $context = null, $settingable_type = null, $settingable_id = null)
    {
        $setting = app(SettingRepository::class)
            ->createSettingInstance($key, $context, $settingable_type, $settingable_id);

        $setting->fill([
            'name' => $key,
            'value' => encrypt($value),
            'context' => $context,
            'settingable_type' => $settingable_type,
            'settingable_id' => $settingable_id
        ]);

        $setting->save();
        return true;
    }

    public function getFormattedDeliverySettings($context)
    {
        return app(SettingRepository::class)->getDeliverySettingLists(
            $context
        );
    }

    public function setDefaultSettings($key, $value,  $context='mail', $settingable_type = null, $settingable_id= null)
    {
        $setting = app(SettingRepository::class)
            ->createSettingInstance($key, $context, $settingable_type, $settingable_id);

        $setting->fill([
            'name' => $key,
            'value' => $value,
            'context' => $context,
            'settingable_type' => $settingable_type,
            'settingable_id' => $settingable_id
        ]);

        return $setting->save();
    }

    public function getDefaultSettings($key = 'default_mail', $settingable_type = null, $settingable_id= null)
    {
        return app(SettingRepository::class)
            ->getDefaultMailKey($key, $settingable_type = null, $settingable_id= null);
    }
}