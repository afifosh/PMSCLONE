<?php


namespace App\Services\Core\Setting\General;


use App\Repositories\SettingRepository;
use App\Services\Core\BaseService;
use App\Traits\FileHandler;

class SettingService extends BaseService
{
    use FileHandler;

    public function update($context = 'app')
    {
        $settings = request()->except('allowed_resource', '_token', '_method');

        return collect(array_keys($settings))->map(function ($key) use ($settings, $context) {
            $setting = app(SettingRepository::class)
                ->createSettingInstance($key, $context);

            $this->handleIfFileDeleted($setting);

            if (is_null(request()->input($key))) {
                return true;
            }

            if (request()->file($key)) {
                $this->deleteImage(optional($setting)->value);
                $settings[$key] = $this->uploadImage(request()->file($key), config('file.' . $key . '.folder'), config('file.' . $key . '.height'));
            }

            $this->setModel($setting);

            if ($locale = request()->get('language')) {
                session()->put('locale', $locale);
            }

            return parent::save([
                'name' => $key,
                'value' => $settings[$key],
                'context' => $context
            ]);
        });
    }


    public function getFormattedSettings($context = 'app')
    {
        return resolve(SettingRepository::class)
            ->getFormattedSettings($context);
    }

    private function handleIfFileDeleted($setting)
    {
        $request = app('request');

        if ($request->has("{$setting->name}-file-deleted")) {
            $setting->delete();
            return true;
        }

        return false;
    }
}
