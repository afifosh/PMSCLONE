<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Innoclapps\Updater\Patcher;
use App\Innoclapps\Updater\Updater;
use Illuminate\Support\Facades\URL;
use App\Innoclapps\Updater\Migration;
use Illuminate\Filesystem\Filesystem;
use App\Innoclapps\Facades\Innoclapps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Innoclapps\Timeline\Timelineables;
use Illuminate\Database\Migrations\Migrator;
use App\Innoclapps\Media\MediaRepositoryEloquent;
use App\Innoclapps\Updater\PatchRepositoryEloquent;
use App\Innoclapps\Filters\FilterRepositoryEloquent;
use App\Innoclapps\Cards\DashboardRepositoryEloquent;
use App\Innoclapps\Permissions\RoleRepositoryEloquent;
use Illuminate\Database\LazyLoadingViolationException;
use App\Innoclapps\Workflow\WorkflowRepositoryEloquent;
use App\Innoclapps\Zapier\ZapierHookRepositoryEloquent;
use App\Innoclapps\Fields\CustomFieldRepositoryEloquent;
use App\Innoclapps\Media\PendingMediaRepositoryEloquent;
use App\Innoclapps\OAuth\OAuthAccountRepositoryEloquent;
use App\Innoclapps\Contracts\Repositories\RoleRepository;
use App\Innoclapps\Contracts\Repositories\MediaRepository;
use App\Innoclapps\Contracts\Repositories\PatchRepository;
use App\Innoclapps\Contracts\Repositories\FilterRepository;
use App\Innoclapps\Contracts\Repositories\ImportRepository;
use App\Innoclapps\Contracts\Repositories\CountryRepository;
use App\Innoclapps\Permissions\PermissionRepositoryEloquent;
use App\Innoclapps\Contracts\Repositories\MailableRepository;
use App\Innoclapps\Contracts\Repositories\WorkflowRepository;
use App\Innoclapps\Resources\Import\ImportRepositoryEloquent;
use App\Innoclapps\Contracts\Repositories\DashboardRepository;
use App\Innoclapps\Contracts\Repositories\PermissionRepository;
use App\Innoclapps\Contracts\Repositories\ZapierHookRepository;
use App\Innoclapps\Contracts\Repositories\CustomFieldRepository;
use App\Innoclapps\MailableTemplates\MailableRepositoryEloquent;
use App\Innoclapps\Contracts\Repositories\OAuthAccountRepository;
use App\Innoclapps\Contracts\Repositories\PendingMediaRepository;
use App\Innoclapps\Timeline\PinnedTimelineSubjectRepositoryEloquent;
use App\Innoclapps\Contracts\Repositories\PinnedTimelineSubjectRepository;

class InnoclappsServiceProvider extends ServiceProvider
{
    /**
    * All of the container bindings that should be registered.
    *
    * @var array
    */
    public $bindings = [
        PermissionRepository::class            => PermissionRepositoryEloquent::class,
        MailableRepository::class              => MailableRepositoryEloquent::class,
        RoleRepository::class                  => RoleRepositoryEloquent::class,
        OAuthAccountRepository::class          => OAuthAccountRepositoryEloquent::class,
        FilterRepository::class                => FilterRepositoryEloquent::class,
        PendingMediaRepository::class          => PendingMediaRepositoryEloquent::class,
        MediaRepository::class                 => MediaRepositoryEloquent::class,
        CustomFieldRepository::class           => CustomFieldRepositoryEloquent::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        'timezone' => \App\Innoclapps\Timezone::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(Migration::class)
            ->needs(Migrator::class)
            ->give(function () {
                return $this->app['migrator'];
            });

        $this->app->singleton(Updater::class, function ($app) {
            return new Updater(new Client, new Filesystem, [
                'purchase_key'      => $app['config']->get('updater.purchase_key'),
                'archive_url'       => $app['config']->get('updater.archive_url'),
                'download_path'     => $app['config']->get('updater.download_path'),
                'version_installed' => $app['config']->get('updater.version_installed'),
                'exclude_folders'   => $app['config']->get('updater.exclude_folders'),
                'exclude_files'     => $app['config']->get('updater.exclude_files'),
                'permissions'       => $app['config']->get('updater.permissions'),
            ]);
        });

        $this->app->singleton(Patcher::class, function ($app) {
            return new Patcher(new Client, new Filesystem, [
                'purchase_key'      => $app['config']->get('updater.purchase_key'),
                'patches_url'       => $app['config']->get('updater.patches_archive_url'),
                'download_path'     => $app['config']->get('updater.download_path'),
                'version_installed' => $app['config']->get('updater.version_installed'),
                'permissions'       => $app['config']->get('updater.permissions'),
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacros();

        Innoclapps::whenInstalled(fn () => Timelineables::discover());

        Request::macro('isForTimeline', fn () => $this->boolean('timeline'));

        // Avoid lazy loading violation when the calls are coming from the repositories delete, restore and forceDelete
        // methods because these methods are using foreach loops to find and delete/restore/forceDelete multiple models
        // However, this is valid only for development installation
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) : void {
            if (! collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->first(function ($trace) {
                return in_array($trace['function'], ['delete', 'forceDelete', 'restore']) &&
                isset($trace['class']) && stripos($trace['class'], 'repository') !== false;
            })) {
                throw new LazyLoadingViolationException($model, $relation);
            }
        });
    }

    /**
     * Register application macros
     *
     * @return void
     */
    public function registerMacros()
    {
        Str::macro('isBase64Encoded', new \App\Innoclapps\Macros\Str\IsBase64Encoded);
        Str::macro('clickable', new \App\Innoclapps\Macros\Str\ClickableUrls);

        Arr::macro('toObject', new \App\Innoclapps\Macros\Arr\ToObject);
        Arr::macro('valuesAsString', new \App\Innoclapps\Macros\Arr\CastValuesAsString);

        Request::macro('isSearching', new \App\Innoclapps\Macros\Request\IsSearching);
        Request::macro('isZapier', new \App\Innoclapps\Macros\Request\IsZapier);

        Filesystem::macro('deepCleanDirectory', new \App\Innoclapps\Macros\Filesystem\DeepCleanDirectory);

        URL::macro('asAppUrl', function ($extra = '') {
            return rtrim(config('app.url'), '/') . ($extra ? '/' . $extra : '');
        });
    }
}
