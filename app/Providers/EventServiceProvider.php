<?php

namespace App\Providers;

use App\Events\EmailAccountMessageCreated;
use App\Innoclapps\OAuth\Events\OAuthAccountConnected;
use App\Listeners\BouncedEmail;
use App\Listeners\CreateEmailAccountViaOAuth;
use App\Listeners\EmailDelivered;
use App\Listeners\EmailLinkClicked;
use App\Listeners\EmailSent;
use App\Listeners\EmailViewed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TwoFactorCodeEvent;
use App\Listeners\TwoFactorCodeListener;
use App\Listeners\SuccessfulLoginListener;
use Laravel\Fortify\Events\TwoFactorLogin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use App\Listeners\IncrementDeviceAuthorizationAttempts;

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
        OAuthAccountConnected::class => [
            CreateEmailAccountViaOAuth::class,
        ],
        // EmailAccountMessageCreated::class => [
        //     CreateContactFromEmailAccountMessage::class,
        //     AttachEmailAccountMessageToContact::class,
        // ],
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
        'App\Events\BroadcastSettingUpdated' => [
            'App\Listeners\CacheBroadcastSetting'
        ],
        'App\Events\SecuritySettingUpdated' => [
            'App\Listeners\CacheSetting'
        ],
        'App\Events\GeneralSettingUpdated' => [
            'App\Listeners\CacheSetting'
        ],
        Failed::class => [
            IncrementDeviceAuthorizationAttempts::class,
        ],

        'Illuminate\Auth\Events\Authenticated' => [
            'App\Listeners\AfterAuthenticatedListener',
        ],

        TwoFactorCodeEvent::class => [TwoFactorCodeListener::class],

        Login::class => [SuccessfulLoginListener::class],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        //
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
