<?php

namespace App\Traits;

use App\Models\EmailService as ModelsEmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait EmailService
{
    /**
     * Fields of email configuration
     * 
     * @var array
     */
    private $emailConfig = [];

    /**
     * Update email service
     * 
     * @param ModelsEmailService $service
     * @param array $filteredRequest
     */
    public function updateEmailService(ModelsEmailService $service, array $filteredRequest): void
    {
        $excludedKeys = ['sent_from_name', 'sent_from_address'];

        collect($filteredRequest)->each(function ($columnValue, $columnName) use ($service, $excludedKeys) {
            if (is_null($columnValue)) {
                return true;
            }

            $DELIMETER = '_';
            $columnName = in_array($columnName, $excludedKeys) ? $columnName : Str::after($columnName, $DELIMETER);

            $this->emailConfig[$columnName] = $columnValue;
        });

        ModelsEmailService::query()->updateOrCreate(['name' => $service->name], [...$this->emailConfig]);

        // update all services to false (not used)
        ModelsEmailService::query()->update(['is_active' => false]);

        // update the service that is being edited to true
        $service->update(['is_active' => true]);

        $this->putEmailConfigInCache($filteredRequest); // put universal email config in cache
    }

    /**
     * Put email configurations used for sending email in cache
     * 
     * @param array $filteredRequest
     * @return void
     */
    private function putEmailConfigInCache(array $filteredRequest): void
    {
        // store in cache
        cache()->store(config('cache.default'))->put(
            'project_email_configurations',
            json_encode($this->emailConfig)
        );
    }
}
