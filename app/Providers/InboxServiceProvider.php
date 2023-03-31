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

namespace App\Providers;

use App\Innoclapps\Facades\Menu;
use App\Innoclapps\Menu\MenuItem;
use Illuminate\Support\Facades\Auth;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Facades\Permissions;
use Illuminate\Support\ServiceProvider;
use App\Repositories\EmailAccountRepositoryEloquent;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Repositories\EmailAccountFolderRepositoryEloquent;
use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;
use App\Repositories\EmailAccountMessageRepositoryEloquent;
use App\Contracts\Repositories\EmailAccountFolderRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Repositories\PredefinedMailTemplateRepositoryEloquent;
use App\Contracts\Repositories\PredefinedMailTemplateRepository;

class InboxServiceProvider extends ServiceProvider
{
    /**
    * All of the container bindings that should be registered.
    *
    * @var array
    */
    public $bindings = [
        EmailAccountRepository::class           => EmailAccountRepositoryEloquent::class,
        EmailAccountFolderRepository::class     => EmailAccountFolderRepositoryEloquent::class,
        EmailAccountMessageRepository::class    => EmailAccountMessageRepositoryEloquent::class,
        PredefinedMailTemplateRepository::class => PredefinedMailTemplateRepositoryEloquent::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Innoclapps::booting(function () {
            $accounts = auth()->check() ? app(EmailAccountRepository::class)
                ->with('oAuthAccount')
                ->pushCriteria(EmailAccountsForUserCriteria::class)
                ->get()->filter->canSendMails() : null;

            Menu::register(
                MenuItem::make(__('inbox.inbox'), '/inbox', 'Mail')
                    ->position(15)
                    ->badge(fn () => resolve(EmailAccountRepository::class)->countUnreadMessagesForUser(Auth::user()))
                    ->inQuickCreate(! is_null($accounts?->filter->isPrimary()->first() ?? $accounts?->first()))
                    ->quickCreateName(__('mail.send'))
                    ->quickCreateRoute('/inbox?compose=true')
                    ->keyboardShortcutChar('E')
                    ->badgeVariant('info')
            );
        });

        $this->registerPermissions();
    }

    /**
     * Register inbox permissions
     *
     * @return void
     */
    public function registerPermissions() : void
    {
        Permissions::register(function ($manager) {
            $manager->group(['name' => 'inbox', 'as' => __('inbox.shared')], function ($manager) {
                $manager->view('access-inbox', [
                    'as'          => __('role.capabilities.access'),
                    'permissions' => [
                        'access shared inbox' => __('role.capabilities.access'),
                    ],
                ]);
            });
        });
    }
}
