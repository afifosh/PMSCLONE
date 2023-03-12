<?php

namespace App\Providers;

use App\Listeners\BouncedEmail;
use App\Listeners\EmailDelivered;
use App\Listeners\EmailLinkClicked;
use App\Listeners\EmailSent;
use App\Listeners\EmailViewed;
use App\Models\Admin;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'jdavidbakr\MailTracker\Events\EmailSentEvent' => [
            EmailSent::class,
        ],
        'jdavidbakr\MailTracker\Events\ViewEmailEvent' => [
            EmailViewed::class,
        ],
        'jdavidbakr\MailTracker\Events\LinkClickedEvent' => [
            EmailLinkClicked::class,
        ],
        'jdavidbakr\MailTracker\Events\EmailDeliveredEvent' => [
            EmailDelivered::class,
        ],
        // 'jdavidbakr\MailTracker\Events\ComplaintMessageEvent' => [
        //     'App\Listeners\EmailComplaint',
        // ],
        'jdavidbakr\MailTracker\Events\PermanentBouncedMessageEvent' => [
            BouncedEmail::class,
        ],
        'App\Events\DeliverySettingUpdated' => [
            'App\Listeners\CacheDeliverySetting'
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        User::class => [UserObserver::class],
        Admin::class => [AdminObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
