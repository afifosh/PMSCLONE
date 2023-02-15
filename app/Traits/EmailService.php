<?php

namespace App\Traits;

use App\Models\EmailService as ModelsEmailService;
use Illuminate\Support\Str;

trait EmailService
{
    private $emailConfig = [];

    public function updateEmailService(ModelsEmailService $service, array $filteredRequest): void
    {
        collect($filteredRequest)->each(function ($fieldValue, $fieldName) use ($service) {
            if (is_null($fieldValue)) {
                return true;
            }

            $DELIMETER = '_';
            $fieldName = Str::after($fieldName, $DELIMETER);

            $this->emailConfig[$fieldName] = $fieldValue;

            $service->emailServiceFields()->updateOrCreate(['field_name' => $fieldName], [
                'field_name' => $fieldName,
                'field_value' => $fieldValue,
            ]);
        });

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
        // figure out extra email configs here and put them in cache to be used later
        $this->emailConfig['address'] = $filteredRequest['email_sent_from_email'];
        $this->emailConfig['name'] = $filteredRequest['email_sent_from_name'];

        // store in cache
        cache()->store(config('cache.default'))->put(
            'project_email_configurations',
            json_encode($this->emailConfig)
        );
    }
}
