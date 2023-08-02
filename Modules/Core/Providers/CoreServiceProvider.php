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

namespace Modules\Core\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modules\Core\DatabaseState;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Facades\MailableTemplates;
use Modules\Core\Media\PruneStaleMediaAttachments;
use Modules\Core\Timeline\Timelineables;

class CoreServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Core';

    protected string $moduleNameLower = 'core';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        Innoclapps::whenReadyForServing(Timelineables::discover(...));

        $this->registerMacros();
        $this->registerCommands();
        $this->scheduleTasks();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        DatabaseState::register([
            \Modules\Core\Database\State\EnsureMailableTemplatesArePresent::class,
            \Modules\Core\Database\State\EnsureCountriesArePresent::class,
        ]);

        $this->app->singleton('timezone', \Modules\Core\Timezone::class);
        $this->app->when(Migration::class)->needs(Migrator::class)->give(fn () => $this->app['migrator']);
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

        foreach (['html_purifier', 'fields', 'settings'] as $config) {
            $this->mergeConfigFrom(
                module_path($this->moduleName, "config/$config.php"),
                $config
            );
        }

    }

    /**
     * Register the core commands.
     */
    public function registerCommands(): void
    {
        $this->commands([
            \Modules\Core\Console\Commands\ClearHtmlPurifierCacheCommand::class,
        ]);
    }

    /**
     * Schedule the document related tasks.
     */
    public function scheduleTasks(): void
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->call(new PruneStaleMediaAttachments)->name('prune-stale-media-attachments')->daily();
    }

    /**
     * Register application macros
     */
    public function registerMacros(): void
    {
        Request::macro('isForTimeline', fn () => Request::boolean('timeline'));

        Request::macro('eagerLoad', function () {
            return Str::of(Request::instance()->get('with', ''))->explode(';')->filter()->all();
        });

        Str::macro('isBase64Encoded', new \Modules\Core\Macros\Str\IsBase64Encoded);
        Str::macro('clickable', new \Modules\Core\Macros\Str\ClickableUrls);

        Arr::macro('toObject', new \Modules\Core\Macros\Arr\ToObject);
        Arr::macro('valuesAsString', new \Modules\Core\Macros\Arr\CastValuesAsString);

        Request::macro('isSearching', new \Modules\Core\Macros\Request\IsSearching);

        Filesystem::macro('deepCleanDirectory', new \Modules\Core\Macros\Filesystem\DeepCleanDirectory);

        \Modules\Core\Macros\Criteria\QueryCriteria::register();

        URL::macro('asAppUrl', function ($extra = '') {
            return rtrim(config('app.url'), '/').($extra ? '/'.$extra : '');
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
