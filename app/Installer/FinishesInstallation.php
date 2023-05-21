<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Installer;

use Illuminate\Support\Facades\Artisan;
use Modules\Core\DatabaseState;
use Modules\Core\Environment;
use Modules\Core\Facades\ChangeLogger;
use Modules\Core\Facades\Innoclapps;

trait FinishesInstallation
{
    /**
     * Finalize the installation
     */
    protected function finalizeInstallation($currency, $country): void
    {
        $errors = '';

        ChangeLogger::disable();

        try {
            Artisan::call('storage:link');
        } catch (\Exception) {
            $errors .= "Failed to create storage symlink.\n";
        }

        // Ensure database state is up to date
        DatabaseState::seed();

        // Save default settings from installation
        settings(['currency' => $currency, 'company_country_id' => $country]);

        if (! Innoclapps::markAsInstalled()) {
            $errors .= 'Failed to create the installed file. ('.Innoclapps::installedFileLocation().").\n";
        }

        Environment::capture([
            '_installed_date' => date('Y-m-d H:i:s'),
        ]);

        Artisan::call('concord:optimize');

        if ($errors !== '') {
            throw new FailedToFinalizeInstallationException($errors);
        }
    }

    /**
     * Migrate the database
     */
    protected function migrate(): void
    {
        Artisan::call('migrate --force');
    }
}
