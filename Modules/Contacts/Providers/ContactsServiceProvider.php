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

namespace Modules\Contacts\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Contacts\Listeners\TransferContactsUserData;
use Modules\Contacts\Resource\Contact\Contact;
use Modules\Core\DatabaseState;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Facades\MailableTemplates;
use Modules\Core\Resource\Events\ResourceRecordCreated;
use Modules\Core\Settings\DefaultSettings;
use Modules\Core\Workflow\Workflows;
use Modules\Users\Events\TransferringUserData;

class ContactsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Contacts';

    protected string $moduleNameLower = 'contacts';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        DatabaseState::register([
            \Modules\Contacts\Database\State\EnsureDefaultFiltersArePresent::class,
            \Modules\Contacts\Database\State\EnsureIndustriesArePresent::class,
            \Modules\Contacts\Database\State\EnsureSourcesArePresent::class,
        ]);

        DefaultSettings::add('require_calling_prefix_on_phones', true);
        DefaultSettings::add('auto_associate_company_to_contact', 1);

        $this->app['events']->listen(TransferringUserData::class, TransferContactsUserData::class);

        $this->app['events']->listen(ResourceRecordCreated::class, function (ResourceRecordCreated $event) {
            if ($event->resource instanceof Contact && (bool) settings('auto_associate_company_to_contact')) {
                $event->model->associateToCompaniesByEmailDomain();
            }
        });

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->app->booted(function () {
            Innoclapps::whenReadyForServing($this->bootModule(...));
        });
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
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Boot the module
     */
    protected function bootModule(): void
    {
        Innoclapps::booting($this->shareDataToScript(...));

        $this->registerResources();
        $this->registerNotifications();
        $this->registerMailables();
        $this->registerWorkflowTriggers();
    }

    /**
     * Register the documents module available resources.
     */
    public function registerResources(): void
    {
        Innoclapps::resources([
            \Modules\Contacts\Resource\Company\Company::class,
            \Modules\Contacts\Resource\Contact\Contact::class,
            \Modules\Contacts\Resource\Source::class,
            \Modules\Contacts\Resource\Industry::class,
        ]);
    }

    /**
     * Register the documents module available notifications.
     */
    public function registerNotifications(): void
    {
        Innoclapps::notifications([
            \Modules\Contacts\Notifications\UserAssignedToCompany::class,
            \Modules\Contacts\Notifications\UserAssignedToContact::class,
        ]);
    }

    /**
     * Register the documents module available mailables.
     */
    public function registerMailables(): void
    {
        MailableTemplates::register([
            \Modules\Contacts\Mail\UserAssignedToCompany::class,
            \Modules\Contacts\Mail\UserAssignedToContact::class,
        ]);
    }

    /**
     * Register the documents module available workflows.
     */
    public function registerWorkflowTriggers(): void
    {
        Workflows::triggers([
            \Modules\Contacts\Workflow\Triggers\CompanyCreated::class,
            \Modules\Contacts\Workflow\Triggers\ContactCreated::class,
        ]);
    }

    /**
     * Share data to script.
     */
    public function shareDataToScript(): void
    {
        Innoclapps::provideToScript([
            'contact_fields_height' => settings('contact_fields_height'),
            'company_fields_height' => settings('company_fields_height'),
        ]);
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
