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

namespace Modules\Activities\Providers;

use App\Http\View\FrontendComposers\Tab;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Modules\Activities\Console\Commands\ActivitiesNotificationsCommand;
use Modules\Activities\Highlights\TodaysActivities;
use Modules\Activities\Listeners\StopRelatedOAuthCalendars;
use Modules\Activities\Listeners\TransferActivitiesUserData;
use Modules\Activities\Support\SyncNextActivity;
use Modules\Activities\Support\ToScriptProvider;
use Modules\Contacts\Resource\Company\Frontend\ViewComponent as CompanyViewComponent;
use Modules\Contacts\Resource\Contact\Frontend\ViewComponent as ContactViewComponent;
use Modules\Core\DatabaseState;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Facades\MailableTemplates;
use Modules\Core\Highlights\Highlights;
use Modules\Core\OAuth\Events\OAuthAccountDeleting;
use Modules\Core\Settings\DefaultSettings;
use Modules\Core\SystemInfo;
use Modules\Deals\Resource\Frontend\ViewComponent as DealViewComponent;
use Modules\Users\Events\TransferringUserData;

class ActivitiesServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Activities';

    protected string $moduleNameLower = 'activities';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        DatabaseState::register([
            \Modules\Activities\Database\State\EnsureDefaultFiltersArePresent::class,
            \Modules\Activities\Database\State\EnsureActivityTypesArePresent::class,
        ]);

        $this->app['events']->listen(OAuthAccountDeleting::class, StopRelatedOAuthCalendars::class);
        $this->app['events']->listen(TransferringUserData::class, TransferActivitiesUserData::class);

        DefaultSettings::add('send_contact_attends_to_activity_mail', false);
        DefaultSettings::addRequired('default_activity_type');

        $this->commands([
            ActivitiesNotificationsCommand::class,
        ]);

        $this->app->booted(function () {
            Innoclapps::whenReadyForServing($this->bootModule(...));
        });

        SystemInfo::register('PREFERRED_DEFAULT_HOUR', $this->app['config']->get('activities.defaults.hour'));
        SystemInfo::register('PREFERRED_DEFAULT_MINUTES', $this->app['config']->get('activities.defaults.minutes'));
        SystemInfo::register('PREFERRED_DEFAULT_REMINDER_MINUTES', $this->app['config']->get('activities.defaults.reminder_minutes'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
    }

    /**
     * Boot the module
     */
    protected function bootModule(): void
    {
        Highlights::register(new TodaysActivities);

        Innoclapps::booting($this->shareDataToScript(...));

        $this->scheduleTasks();
        $this->registerResources();
        $this->registerNotifications();
        $this->registerMailables();
        $this->registerRelatedRecordsDetailTab();
    }

    /**
     * Schedule the module tasks.
     */
    protected function scheduleTasks(): void
    {
        // $schedule = $this->app->make(Schedule::class);

        // $schedule->call(function () {
        //     (new SyncNextActivity)->__invoke();
        // })
        //     ->name('sync-next-activity')
        //     ->everyFiveMinutes()
        //     ->withoutOverlapping(5);

        // if (function_exists('proc_open') && function_exists('proc_close')) {
        //     $schedule->command('activities:due-notifications')->everyMinute()->withoutOverlapping(5);
        // } else {
        //     $schedule->call(function () {
        //         Artisan::call('activities:due-notifications');
        //     })
        //         ->everyMinute()
        //         ->name('notify-due-activities')
        //         ->withoutOverlapping(5);
        // }
    }

    /**
     * Register the module available resources.
     */
    protected function registerResources(): void
    {
        Innoclapps::resources([
            \Modules\Activities\Resource\Activity::class,
            \Modules\Activities\Resource\ActivityType::class,
        ]);
    }

    /**
     * Register the activities module available notifications.
     */
    protected function registerNotifications(): void
    {
        Innoclapps::notifications([
            \Modules\Activities\Notifications\ActivityReminder::class,
            \Modules\Activities\Notifications\UserAssignedToActivity::class,
            \Modules\Activities\Notifications\UserAttendsToActivity::class,
        ]);
    }

    /**
     * Register the module available mailables.
     */
    protected function registerMailables(): void
    {
        MailableTemplates::register([
            \Modules\Activities\Mail\ActivityReminder::class,
            \Modules\Activities\Mail\ContactAttendsToActivity::class,
            \Modules\Activities\Mail\UserAssignedToActivity::class,
            \Modules\Activities\Mail\UserAttendsToActivity::class,
        ]);
    }

    /**
     * Register the module related tabs.
     */
    protected function registerRelatedRecordsDetailTab(): void
    {
        $tab = Tab::make('activities', 'activities-tab')->panel('activities-tab-panel')->order(15);

        // ContactViewComponent::registerTab($tab);
        // CompanyViewComponent::registerTab($tab);
        // DealViewComponent::registerTab($tab);
    }

    /**
     * Share data to script.
     */
    protected function shareDataToScript(): void
    {
        Innoclapps::provideToScript(call_user_func(new ToScriptProvider));
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the publishable view paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach ($this->app['config']->get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
