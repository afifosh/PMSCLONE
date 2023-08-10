<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Facades\Permissions;
use Modules\Core\OAuth\Events\OAuthAccountConnected;
use Modules\Core\OAuth\Events\OAuthAccountDeleting;
use Modules\MailClient\Client\ClientManager;
use Modules\MailClient\Client\ConnectionType;
use Modules\MailClient\Client\FolderType;
use Modules\MailClient\Console\Commands\EmailAccountsSyncCommand;
// use Modules\MailClient\Events\EmailAccountMessageCreated;
// use Modules\MailClient\Listeners\CreateContactFromEmailAccountMessage;
use Modules\MailClient\Listeners\CreateEmailAccountViaOAuth;
use Modules\MailClient\Listeners\StopRelatedOAuthEmailAccounts;
use Modules\MailClient\Models\EmailAccount;

class MailClientServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'MailClient';

    protected string $moduleNameLower = 'mailclient';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerPermissions();

        $this->app['events']->listen(OAuthAccountConnected::class, CreateEmailAccountViaOAuth::class);
        $this->app['events']->listen(OAuthAccountDeleting::class, StopRelatedOAuthEmailAccounts::class);
        // $this->app['events']->listen(EmailAccountMessageCreated::class, CreateContactFromEmailAccountMessage::class);

        $this->app->booted(function () {
            $this->registerResources();
            Innoclapps::whenReadyForServing($this->bootModule(...));
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->commands([
            EmailAccountsSyncCommand::class,
        ]);
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
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Boot the mail client module.
     */
    protected function bootModule(): void
    {
        Innoclapps::booting($this->shareDataToScript(...));

        $this->scheduleTasks();
        $this->registerResources();
    }

    /**
     * Schedule the document related tasks.
     */
    public function scheduleTasks(): void
    {
        /** @var \Illuminate\Console\Scheduling\Schedule */
        $schedule = $this->app->make(Schedule::class);
        $syncCommandCronExpression = config('mailclient.sync.interval');
        $syncCommandName = 'sync-email-accounts';

        // if (Innoclapps::canRunProcess()) {
        if (function_exists('proc_open') && function_exists('proc_close')){
            $schedule->command(EmailAccountsSyncCommand::class, ['--broadcast', '--isolated' => 5])
                ->cron($syncCommandCronExpression)
                ->name($syncCommandName)
                ->withoutOverlapping(30)
                ->runInBackground();
        } else {
            $schedule->call(function () {
                Artisan::call(EmailAccountsSyncCommand::class, ['--broadcast' => true, '--isolated' => 5]);
            })
                ->cron($syncCommandCronExpression)
                ->name($syncCommandName)
                ->withoutOverlapping(30)
                ->runInBackground();
        }
    }

    /**
     * Register the mail client module resources.
     */
    public function registerResources(): void
    {
        Innoclapps::resources([
            \Modules\MailClient\Resource\EmailMessage::class,
        ]);
    }

    /**
     * Share data to script.
     */
    protected function shareDataToScript(): void
    {
        Innoclapps::provideToScript(['mail' => [
            'reply_prefix' => config('mailclient.reply_prefix'),
            'forward_prefix' => config('mailclient.forward_prefix'),
            'accounts' => [
                'connections' => ConnectionType::cases(),
                'encryptions' => ClientManager::ENCRYPTION_TYPES,
                'from_name' => EmailAccount::DEFAULT_FROM_NAME_HEADER,
            ],
            'folders' => [
                'outgoing' => FolderType::outgoingTypes(),
                'incoming' => FolderType::incomingTypes(),
                'other' => FolderType::OTHER,
                'drafts' => FolderType::DRAFTS,
            ],
        ],
        ]);
    }

    /**
     * Register the mail client module permissions.
     */
    protected function registerPermissions(): void
    {
        Permissions::register(function ($manager) {
            $manager->group(['name' => 'inbox', 'as' => __('mailclient::inbox.shared')], function ($manager) {
                $manager->view('access-inbox', [
                    'as' => __('core::role.capabilities.access'),
                    'permissions' => [
                        'access shared inbox' => __('core::role.capabilities.access'),
                    ],
                ]);
            });
        });
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
