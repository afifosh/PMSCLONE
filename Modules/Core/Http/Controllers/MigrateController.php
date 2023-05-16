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

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Modules\Core\Facades\Innoclapps;

class MigrateController extends Controller
{
    /**
     * Show the migration required action.
     */
    public function show(): View
    {
        abort_unless(Innoclapps::requiresMigration(), 404);

        return view('core::migrate');
    }

    /**
     * Perform migration.
     */
    public function migrate(): void
    {
        abort_unless(Innoclapps::requiresMigration(), 404);

        Artisan::call('migrate --force');
    }
}
